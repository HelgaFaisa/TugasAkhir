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
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Query santri aktif yang punya transaksi (atau semua jika tidak ada filter)
        $santriQuery = Santri::aktif()
            ->select('id_santri', 'nama_lengkap')
            ->withCount(['uangSaku as transaksi_bulan_ini' => function ($q) {
                $q->whereMonth('tanggal_transaksi', now()->month)
                  ->whereYear('tanggal_transaksi', now()->year);
            }])
            ->has('uangSaku');

        if ($search) {
            $santriQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('id_santri', 'like', "%{$search}%");
            });
        }

        $santriList = $santriQuery->orderBy('nama_lengkap')
            ->paginate(20)
            ->appends(request()->query());

        // Ambil saldo terakhir & transaksi terbaru per santri (batch)
        $ids = $santriList->pluck('id_santri');

        // Saldo terakhir per santri (dari transaksi terbaru)
        $saldoMap = UangSaku::whereIn('id_santri', $ids)
            ->select('id_santri', 'saldo_sesudah')
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->get()
            ->unique('id_santri')
            ->keyBy('id_santri');

        // Transaksi terbaru per santri (max 5)
        $transaksiMap = UangSaku::whereIn('id_santri', $ids)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('id_santri')
            ->map(fn ($group) => $group->take(5));

        // Attach ke santri objects
        $santriList->getCollection()->each(function ($santri) use ($saldoMap, $transaksiMap) {
            $santri->saldo_terakhir = $saldoMap[$santri->id_santri]->saldo_sesudah ?? 0;
            $santri->transaksi_terbaru = $transaksiMap[$santri->id_santri] ?? collect();
        });

        return view('admin.uang-saku.index', compact('santriList'));
    }

    /**
     * AJAX: Info santri untuk form create/edit
     */
    public function santriInfo($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();

        $bulanIni = now();

        // Saldo terakhir
        $lastTx = UangSaku::where('id_santri', $id_santri)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->first();

        $saldo = $lastTx ? $lastTx->saldo_sesudah : 0;

        // Total pemasukan & pengeluaran bulan ini
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

        // 3 transaksi terakhir
        $transaksiTerakhir = UangSaku::where('id_santri', $id_santri)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(fn ($t) => [
                'tanggal'    => $t->tanggal_transaksi->format('d/m/Y'),
                'jenis'      => $t->jenis_transaksi,
                'nominal'    => number_format($t->nominal, 0, ',', '.'),
                'keterangan' => $t->keterangan ?? '-',
            ]);

        return response()->json([
            'nama'                     => $santri->nama_lengkap,
            'saldo_terakhir'           => number_format($saldo, 0, ',', '.'),
            'saldo_raw'                => $saldo,
            'total_pemasukan_bulan_ini' => number_format($pemasukanBulanIni, 0, ',', '.'),
            'total_pengeluaran_bulan_ini' => number_format($pengeluaranBulanIni, 0, ',', '.'),
            'transaksi_terakhir'       => $transaksiTerakhir,
        ]);
    }

    /**
     * Form tambah transaksi
     */
    public function create()
    {
        $santriList = Santri::where('status', 'Aktif')
            ->select('id_santri', 'nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.uang-saku.create', compact('santriList'));
    }

    /**
     * Simpan transaksi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'nominal' => 'required|numeric|min:1|max:99999999',
            'keterangan' => 'nullable|string|max:500',
            'tanggal_transaksi' => 'required|date',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists' => 'Santri tidak ditemukan.',
            'jenis_transaksi.required' => 'Jenis transaksi wajib dipilih.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            UangSaku::create($validated);
            
            // Update saldo transaksi berikutnya jika ada
            $this->recalculateSaldoAfter($validated['id_santri'], $validated['tanggal_transaksi']);
            
            DB::commit();
            Cache::forget('santri_aktif_uang_saku');
            
            return redirect()->route('admin.uang-saku.index')
                ->with('success', 'Transaksi uang saku berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail transaksi
     */
    public function show($id)
    {
        $transaksi = UangSaku::with('santri')->findOrFail($id);
        return view('admin.uang-saku.show', compact('transaksi'));
    }

    /**
     * Form edit transaksi
     */
    public function edit($id)
    {
        $transaksi = UangSaku::with('santri')->findOrFail($id);
        
        $santriList = Santri::where('status', 'Aktif')
            ->select('id_santri', 'nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.uang-saku.edit', compact('transaksi', 'santriList'));
    }

    /**
     * Update transaksi
     */
    public function update(Request $request, $id)
    {
        $transaksi = UangSaku::findOrFail($id);

        $validated = $request->validate([
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'nominal' => 'required|numeric|min:1|max:99999999',
            'keterangan' => 'nullable|string|max:500',
            'tanggal_transaksi' => 'required|date',
        ], [
            'jenis_transaksi.required' => 'Jenis transaksi wajib dipilih.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $transaksi->update($validated);
            
            // Recalculate semua saldo setelah transaksi ini
            $this->recalculateSaldoAfter($transaksi->id_santri, $transaksi->tanggal_transaksi);
            
            DB::commit();
            Cache::forget('santri_aktif_uang_saku');
            
            return redirect()->route('admin.uang-saku.index')
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Hapus transaksi
     */
    public function destroy($id)
    {
        $transaksi = UangSaku::findOrFail($id);
        $idSantri = $transaksi->id_santri;
        $tanggal = $transaksi->tanggal_transaksi;

        DB::beginTransaction();
        try {
            $transaksi->delete();
            
            // Recalculate saldo setelah transaksi dihapus
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

    /**
     * Tampilkan riwayat uang saku per santri dengan filter tanggal
     */
    public function riwayat(Request $request, $id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        
        // Default: bulan ini
        $tanggalDari = $request->filled('tanggal_dari') 
            ? $request->tanggal_dari 
            : now()->startOfMonth()->format('Y-m-d');
        
        $tanggalSampai = $request->filled('tanggal_sampai') 
            ? $request->tanggal_sampai 
            : now()->endOfMonth()->format('Y-m-d');
        
        // Query transaksi dengan filter tanggal
        $query = UangSaku::where('id_santri', $id_santri);
        
        if ($tanggalDari && $tanggalSampai) {
            $query->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai]);
        }
        
        $transaksi = $query->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Statistik dengan filter tanggal
        $totalPemasukan = UangSaku::where('id_santri', $id_santri)
            ->where('jenis_transaksi', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai])
            ->sum('nominal');

        $totalPengeluaran = UangSaku::where('id_santri', $id_santri)
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai])
            ->sum('nominal');

        // Saldo terakhir tetap dari keseluruhan transaksi
        $saldoTerakhir = $santri->saldo_uang_saku;

        // Data untuk grafik dengan filter tanggal
        $dataGrafik = UangSaku::where('id_santri', $id_santri)
            ->whereBetween('tanggal_transaksi', [$tanggalDari, $tanggalSampai])
            ->select(
                DB::raw('DATE(tanggal_transaksi) as tanggal'),
                DB::raw('SUM(CASE WHEN jenis_transaksi = "pemasukan" THEN nominal ELSE 0 END) as pemasukan'),
                DB::raw('SUM(CASE WHEN jenis_transaksi = "pengeluaran" THEN nominal ELSE 0 END) as pengeluaran')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Jika tidak ada transaksi di rentang tanggal, buat data kosong
        if ($dataGrafik->isEmpty()) {
            $dataGrafik = collect([
                (object)[
                    'tanggal' => $tanggalDari,
                    'pemasukan' => 0,
                    'pengeluaran' => 0
                ]
            ]);
        }

        // Info periode
        $periodeDari = \Carbon\Carbon::parse($tanggalDari);
        $periodeSampai = \Carbon\Carbon::parse($tanggalSampai);
        
        return view('admin.uang-saku.riwayat', compact(
            'santri',
            'transaksi',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoTerakhir',
            'dataGrafik',
            'tanggalDari',
            'tanggalSampai',
            'periodeDari',
            'periodeSampai'
        ));
    }

    /**
     * Helper: Recalculate saldo untuk transaksi setelah tanggal tertentu
     */
    private function recalculateSaldoAfter($idSantri, $tanggal)
    {
        $transaksiSetelah = UangSaku::where('id_santri', $idSantri)
            ->where('tanggal_transaksi', '>=', $tanggal)
            ->orderBy('tanggal_transaksi')
            ->orderBy('created_at')
            ->get();

        foreach ($transaksiSetelah as $index => $trans) {
            if ($index === 0) {
                // Transaksi pertama: ambil saldo dari transaksi sebelumnya
                $saldoSebelumnya = UangSaku::where('id_santri', $idSantri)
                    ->where('id', '<', $trans->id)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $trans->saldo_sebelum = $saldoSebelumnya ? $saldoSebelumnya->saldo_sesudah : 0;
            } else {
                $trans->saldo_sebelum = $transaksiSetelah[$index - 1]->saldo_sesudah;
            }

            if ($trans->jenis_transaksi === 'pemasukan') {
                $trans->saldo_sesudah = $trans->saldo_sebelum + $trans->nominal;
            } else {
                $trans->saldo_sesudah = $trans->saldo_sebelum - $trans->nominal;
            }

            $trans->saveQuietly(); // Save tanpa trigger event
        }
    }
}