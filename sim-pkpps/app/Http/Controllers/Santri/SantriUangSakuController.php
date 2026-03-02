<?php
// app/Http/Controllers/Santri/SantriUangSakuController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UangSaku;
use App\Models\Santri;

class SantriUangSakuController extends Controller
{
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    /**
     * Tampilkan riwayat uang saku santri yang sedang login
     */
    public function index(Request $request)
    {
        try {
            $idSantri = $this->getSantriId();

            $santri = Santri::with(['kelasPrimary.kelas'])
                ->where('id_santri', $idSantri)
                ->firstOrFail();

            // -- Query uang saku --
            $query = UangSaku::where('id_santri', $idSantri);

            // -- Filter jenis transaksi --
            if ($request->filled('jenis_transaksi')) {
                $query->where('jenis_transaksi', $request->jenis_transaksi);
            }

            // -- Filter tanggal --
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
            }

            // -- Search keterangan --
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhere('id_uang_saku', 'like', "%{$search}%");
                });
            }

            $riwayatUangSaku = $query->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            // -- Statistik: bulan ini atau sesuai filter tanggal --
            $statistikQuery = UangSaku::where('id_santri', $idSantri);

            if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
                if ($request->filled('tanggal_dari')) {
                    $statistikQuery->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
                }
                if ($request->filled('tanggal_sampai')) {
                    $statistikQuery->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
                }
            } else {
                $statistikQuery->whereMonth('tanggal_transaksi', now()->month)
                               ->whereYear('tanggal_transaksi', now()->year);
            }

            $totalPemasukan  = (clone $statistikQuery)->where('jenis_transaksi', 'pemasukan')->sum('nominal');
            $totalPengeluaran = (clone $statistikQuery)->where('jenis_transaksi', 'pengeluaran')->sum('nominal');
            $saldoTerakhir   = $santri->saldo_uang_saku;

            return view('santri.uang-saku.index', compact(
                'riwayatUangSaku',
                'santri',
                'totalPemasukan',
                'totalPengeluaran',
                'saldoTerakhir'
            ));

        } catch (\Exception $e) {
            Log::error('Error Riwayat Uang Saku: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data uang saku.');
        }
    }

    /**
     * Tampilkan detail satu transaksi
     */
    public function show($id)
    {
        try {
            $idSantri = $this->getSantriId();

            // Pastikan transaksi ini milik santri yang login
            $transaksi = UangSaku::where('id', $id)
                ->where('id_santri', $idSantri)
                ->with(['santri' => function ($q) {
                    $q->with('kelasPrimary.kelas')
                      ->select('id_santri', 'nama_lengkap');
                }])
                ->firstOrFail();

            return view('santri.uang-saku.show', compact('transaksi'));

        } catch (\Exception $e) {
            Log::error('Error Detail Uang Saku: ' . $e->getMessage());
            return back()->with('error', 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.');
        }
    }
}