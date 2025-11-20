<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UangSaku;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UangSakuController extends Controller
{
    /**
     * Tampilkan daftar transaksi uang saku
     */
    public function index(Request $request)
    {
        $query = UangSaku::with('santri:id_santri,nama_lengkap');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter berdasarkan santri
        if ($request->filled('id_santri')) {
            $query->bySantri($request->id_santri);
        }

        // Filter berdasarkan jenis transaksi
        if ($request->filled('jenis_transaksi')) {
            $query->byJenis($request->jenis_transaksi);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->byDateRange($request->tanggal_dari, $request->tanggal_sampai);
        }

        $transaksi = $query->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        // Cache santri list untuk dropdown
        $santriList = Cache::remember('santri_aktif_uang_saku', 300, function () {
            return Santri::where('status', 'Aktif')
                ->select('id_santri', 'nama_lengkap')
                ->orderBy('nama_lengkap')
                ->get();
        });

        return view('admin.uang-saku.index', compact('transaksi', 'santriList'));
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