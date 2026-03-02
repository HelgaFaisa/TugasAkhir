<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UangSaku;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UangSakuController extends Controller
{
    /**
     * Tampilkan daftar uang saku — Grouped per Santri
     * Default: bulan ini
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // ── Default: bulan ini ──────────────────────────────────────
        $dari   = $request->get('dari',   now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->endOfMonth()->format('Y-m-d'));
        $sort   = $request->get('sort', 'nama'); // nama | saldo_asc | saldo_desc | transaksi_desc | terakhir

        // ── KPI ringkasan periode (dipengaruhi filter tanggal) ──────
        $kpiQuery = UangSaku::whereBetween('tanggal_transaksi', [$dari, $sampai]);
        $kpi = [
            'total_transaksi'   => (clone $kpiQuery)->count(),
            'total_pemasukan'   => (float)(clone $kpiQuery)->where('jenis_transaksi', 'pemasukan')->sum('nominal'),
            'total_pengeluaran' => (float)(clone $kpiQuery)->where('jenis_transaksi', 'pengeluaran')->sum('nominal'),
            'total_santri'      => (clone $kpiQuery)->distinct('id_santri')->count('id_santri'),
        ];
        // Selisih periode: apakah dalam rentang ini uang yang masuk lebih besar dari yang keluar
        $kpi['selisih'] = $kpi['total_pemasukan'] - $kpi['total_pengeluaran'];

        // ── KPI Real-time: Total saldo aktual seluruh santri (tidak dipengaruhi filter) ──
        // Ambil saldo_sesudah dari transaksi TERAKHIR masing-masing santri
        $totalSaldoSemua = DB::table('uang_saku as u1')
            ->join(DB::raw('(
                SELECT id_santri, MAX(id) as max_id
                FROM uang_saku
                GROUP BY id_santri
            ) as latest'), function ($join) {
                $join->on('u1.id_santri', '=', 'latest.id_santri')
                     ->on('u1.id', '=', 'latest.max_id');
            })
            ->sum('u1.saldo_sesudah');

        $kpi['total_saldo_realtime'] = (float) $totalSaldoSemua;

        // ── Query santri ────────────────────────────────────────────
        $santriQuery = Santri::aktif()
            ->select('id_santri', 'nama_lengkap')
            ->has('uangSaku');

        if ($search) {
            $santriQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('id_santri', 'like', "%{$search}%");
            });
        }

        $santriQuery->orderBy('nama_lengkap');

        $santriList = $santriQuery->paginate(20)->appends(request()->query());

        $ids = $santriList->pluck('id_santri');

        // ── Saldo terakhir per santri (efisien: subquery per-id) ────
        // Ambil id transaksi terakhir per santri lalu join, hindari get()->unique() yang boros
        $latestIds = DB::table('uang_saku')
            ->whereIn('id_santri', $ids)
            ->select('id_santri', DB::raw('MAX(id) as max_id'))
            ->groupBy('id_santri')
            ->pluck('max_id', 'id_santri');

        $saldoMap = UangSaku::whereIn('id', $latestIds->values())
            ->get()
            ->keyBy('id_santri');

        // ── Pemasukan & pengeluaran bulan ini per santri ────────────
        $bulanIniStats = UangSaku::whereIn('id_santri', $ids)
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->select(
                'id_santri',
                DB::raw('SUM(CASE WHEN jenis_transaksi="pemasukan"  THEN nominal ELSE 0 END) as pemasukan_bulan'),
                DB::raw('SUM(CASE WHEN jenis_transaksi="pengeluaran" THEN nominal ELSE 0 END) as pengeluaran_bulan'),
                DB::raw('COUNT(*) as total_bulan')
            )
            ->groupBy('id_santri')
            ->get()
            ->keyBy('id_santri');

        // ── Transaksi terbaru per santri (max 5, untuk collapsed detail) ──
        $transaksiMap = UangSaku::whereIn('id_santri', $ids)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('id_santri')
            ->map(fn($g) => $g->take(5));

        // ── Attach semua data ke santri objects ─────────────────────
        $collection = $santriList->getCollection()->map(function ($santri) use ($saldoMap, $bulanIniStats, $transaksiMap) {
            $saldoRow = $saldoMap[$santri->id_santri] ?? null;
            $bulan    = $bulanIniStats[$santri->id_santri] ?? null;

            $santri->saldo_terakhir         = $saldoRow ? (float)$saldoRow->saldo_sesudah : 0;
            $santri->transaksi_terakhir_tgl  = $saldoRow ? $saldoRow->tanggal_transaksi : null;
            $santri->pemasukan_bulan         = $bulan ? (float)$bulan->pemasukan_bulan   : 0;
            $santri->pengeluaran_bulan       = $bulan ? (float)$bulan->pengeluaran_bulan : 0;
            $santri->transaksi_bulan_ini     = $bulan ? (int)$bulan->total_bulan          : 0;
            $santri->transaksi_terbaru       = $transaksiMap[$santri->id_santri] ?? collect();
            return $santri;
        });

        // ── Re-sort collection setelah attach ───────────────────────
        $sorted = match($sort) {
            'saldo_asc'       => $collection->sortBy('saldo_terakhir'),
            'saldo_desc'      => $collection->sortByDesc('saldo_terakhir'),
            'transaksi_desc'  => $collection->sortByDesc('transaksi_bulan_ini'),
            'terakhir'        => $collection->sortByDesc('transaksi_terakhir_tgl'),
            default           => $collection->sortBy('nama_lengkap'),
        };

        $santriList->setCollection($sorted->values());

        return view('admin.uang-saku.index', compact('santriList', 'kpi', 'dari', 'sampai', 'sort'));
    }

    /**
     * AJAX: Info santri untuk form create/edit
     */
    public function santriInfo($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();

        $bulanIni = now();

        $lastTx = UangSaku::where('id_santri', $id_santri)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->first();

        $saldo = $lastTx ? (float)$lastTx->saldo_sesudah : 0;

        $pemasukanBulanIni = UangSaku::where('id_santri', $id_santri)
            ->where('jenis_transaksi', 'pemasukan')
            ->whereMonth('tanggal_transaksi', $bulanIni->month)
            ->whereYear('tanggal_transaksi', $bulanIni->year)
            ->sum('nominal');

        $pengeluaranBulanIni = UangSaku::where('id_santri', $id_santri)
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereMonth('tanggal_transaksi', $bulanIni->month)
            ->whereYear('tanggal_transaksi', $bulanIni->year)
            ->sum('nominal');

        $transaksiTerakhir = UangSaku::where('id_santri', $id_santri)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(fn($t) => [
                'tanggal'    => $t->tanggal_transaksi->format('d/m/Y'),
                'jenis'      => $t->jenis_transaksi,
                'nominal'    => number_format($t->nominal, 0, ',', '.'),
                'keterangan' => $t->keterangan ?? '-',
            ]);

        return response()->json([
            'nama'                        => $santri->nama_lengkap,
            'saldo_terakhir'              => number_format($saldo, 0, ',', '.'),
            'saldo_raw'                   => $saldo,
            'total_pemasukan_bulan_ini'   => number_format($pemasukanBulanIni, 0, ',', '.'),
            'total_pengeluaran_bulan_ini' => number_format($pengeluaranBulanIni, 0, ',', '.'),
            'transaksi_terakhir'          => $transaksiTerakhir,
        ]);
    }

    public function create()
    {
        $santriList = Santri::where('status', 'Aktif')
            ->select('id_santri', 'nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.uang-saku.create', compact('santriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri'         => 'required|exists:santris,id_santri',
            'jenis_transaksi'   => 'required|in:pemasukan,pengeluaran',
            'nominal'           => 'required|numeric|min:1|max:99999999',
            'keterangan'        => 'nullable|string|max:500',
            'tanggal_transaksi' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            UangSaku::create($validated);
            $this->recalculateSaldoAfter($validated['id_santri'], $validated['tanggal_transaksi']);
            DB::commit();
            Cache::forget('santri_aktif_uang_saku');
            return redirect()->route('admin.uang-saku.index')
                ->with('success', 'Transaksi uang saku berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaksi = UangSaku::with('santri')->findOrFail($id);
        return view('admin.uang-saku.show', compact('transaksi'));
    }

    public function edit($id)
    {
        $transaksi  = UangSaku::with('santri')->findOrFail($id);
        $santriList = Santri::where('status', 'Aktif')
            ->select('id_santri', 'nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();
        return view('admin.uang-saku.edit', compact('transaksi', 'santriList'));
    }

    public function update(Request $request, $id)
    {
        $transaksi = UangSaku::findOrFail($id);
        $validated = $request->validate([
            'jenis_transaksi'   => 'required|in:pemasukan,pengeluaran',
            'nominal'           => 'required|numeric|min:1|max:99999999',
            'keterangan'        => 'nullable|string|max:500',
            'tanggal_transaksi' => 'required|date',
        ]);

        // Simpan tanggal lama sebelum update, agar recalculate dimulai dari yang paling awal
        $tanggalLama = $transaksi->tanggal_transaksi->format('Y-m-d');

        DB::beginTransaction();
        try {
            // Gunakan saveQuietly agar model boot (updating) tidak ikut menghitung ulang saldo
            // — recalculate akan dikerjakan secara menyeluruh oleh recalculateSaldoAfter()
            $transaksi->fill($validated)->saveQuietly();

            // Recalculate dari tanggal yang paling awal antara tanggal lama dan baru
            $tanggalBaru = $validated['tanggal_transaksi'];
            $tanggalMulai = min($tanggalLama, $tanggalBaru);

            $this->recalculateSaldoAfter($transaksi->id_santri, $tanggalMulai);
            DB::commit();
            Cache::forget('santri_aktif_uang_saku');
            return redirect()->route('admin.uang-saku.index')
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $transaksi = UangSaku::findOrFail($id);
        $idSantri  = $transaksi->id_santri;
        $tanggal   = $transaksi->tanggal_transaksi->format('Y-m-d');

        DB::beginTransaction();
        try {
            $transaksi->delete();
            $this->recalculateSaldoAfter($idSantri, $tanggal);
            DB::commit();
            Cache::forget('santri_aktif_uang_saku');
            return redirect()->route('admin.uang-saku.index')
                ->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    public function riwayat(Request $request, $id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();

        $tanggalDari   = $request->filled('tanggal_dari')   ? $request->tanggal_dari   : now()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $request->filled('tanggal_sampai') ? $request->tanggal_sampai : now()->endOfMonth()->format('Y-m-d');

        $query = UangSaku::where('id_santri', $id_santri)
            ->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai]);

        $transaksi = $query->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $totalPemasukan = UangSaku::where('id_santri', $id_santri)
            ->where('jenis_transaksi', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai])
            ->sum('nominal');

        $totalPengeluaran = UangSaku::where('id_santri', $id_santri)
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai])
            ->sum('nominal');

        // Ambil saldo aktual dari transaksi TERAKHIR santri ini (real-time, bukan dari filter)
        $lastTx = UangSaku::where('id_santri', $id_santri)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->first();
        $saldoTerakhir = $lastTx ? (float)$lastTx->saldo_sesudah : 0;

        $dataGrafik = UangSaku::where('id_santri', $id_santri)
            ->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai])
            ->select(
                DB::raw('DATE(tanggal_transaksi) as tanggal'),
                DB::raw('SUM(CASE WHEN jenis_transaksi="pemasukan"  THEN nominal ELSE 0 END) as pemasukan'),
                DB::raw('SUM(CASE WHEN jenis_transaksi="pengeluaran" THEN nominal ELSE 0 END) as pengeluaran')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        if ($dataGrafik->isEmpty()) {
            $dataGrafik = collect([(object)['tanggal' => $tanggalDari, 'pemasukan' => 0, 'pengeluaran' => 0]]);
        }

        $periodeDari   = Carbon::parse($tanggalDari);
        $periodeSampai = Carbon::parse($tanggalSampai);

        return view('admin.uang-saku.riwayat', compact(
            'santri', 'transaksi',
            'totalPemasukan', 'totalPengeluaran', 'saldoTerakhir',
            'dataGrafik', 'tanggalDari', 'tanggalSampai',
            'periodeDari', 'periodeSampai'
        ));
    }

    /**
     * Hitung ulang saldo_sebelum & saldo_sesudah untuk semua transaksi
     * milik $idSantri yang tanggalnya >= $tanggal.
     *
     * Dipanggil setelah store / update / destroy agar urutan saldo
     * tetap konsisten meski transaksi disisipkan di tengah.
     */
    private function recalculateSaldoAfter($idSantri, $tanggal)
    {
        // Pastikan format tanggal string (bukan Carbon object)
        $tanggal = $tanggal instanceof \Carbon\Carbon
            ? $tanggal->format('Y-m-d')
            : $tanggal;

        $transaksiSetelah = UangSaku::where('id_santri', $idSantri)
            ->where('tanggal_transaksi', '>=', $tanggal)
            ->orderBy('tanggal_transaksi')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        foreach ($transaksiSetelah as $index => $trans) {
            if ($index === 0) {
                // Cari saldo_sesudah transaksi tepat sebelum batch ini
                $prev = UangSaku::where('id_santri', $idSantri)
                    ->where('tanggal_transaksi', '<', $tanggal)
                    ->orderByDesc('tanggal_transaksi')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->first();
                $trans->saldo_sebelum = $prev ? (float)$prev->saldo_sesudah : 0;
            } else {
                $trans->saldo_sebelum = (float)$transaksiSetelah[$index - 1]->saldo_sesudah;
            }

            $trans->saldo_sesudah = $trans->jenis_transaksi === 'pemasukan'
                ? $trans->saldo_sebelum + (float)$trans->nominal
                : $trans->saldo_sebelum - (float)$trans->nominal;

            $trans->saveQuietly();
        }
    }
}