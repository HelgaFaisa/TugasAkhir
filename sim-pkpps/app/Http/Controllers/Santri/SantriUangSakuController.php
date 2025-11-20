<?php
// app/Http/Controllers/Santri/SantriUangSakuController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UangSaku;
use App\Models\Santri;

class SantriUangSakuController extends Controller
{
    /**
     * Tampilkan riwayat uang saku santri yang sedang login
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validasi role
            if (!in_array($user->role, ['santri', 'wali'])) {
                abort(403, 'Akses ditolak');
            }
            
            // Ambil data santri
            $santri = Santri::where('id_santri', $user->role_id)->first();
            
            if (!$santri) {
                abort(404, 'Data santri tidak ditemukan');
            }
            
            // Query uang saku dengan pagination dan filter
            $query = UangSaku::where('id_santri', $santri->id_santri)
                ->with('santri:id_santri,nama_lengkap,kelas');
            
            // Filter berdasarkan jenis transaksi
            if ($request->filled('jenis_transaksi')) {
                $query->where('jenis_transaksi', $request->jenis_transaksi);
            }
            
            // Filter berdasarkan tanggal
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
            }
            
            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhere('id_uang_saku', 'like', "%{$search}%");
                });
            }
            
            // Urutkan dari yang terbaru
            $query->orderBy('tanggal_transaksi', 'desc')
                  ->orderBy('created_at', 'desc');
            
            // Pagination
            $riwayatUangSaku = $query->paginate(15)->withQueryString();
            
            // ✅ Hitung statistik berdasarkan filter atau bulan ini
            $statistikQuery = UangSaku::where('id_santri', $santri->id_santri);
            
            // Jika ada filter tanggal, gunakan filter tersebut
            if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
                if ($request->filled('tanggal_dari')) {
                    $statistikQuery->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
                }
                if ($request->filled('tanggal_sampai')) {
                    $statistikQuery->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
                }
            } else {
                // Jika tidak ada filter, tampilkan data bulan ini saja
                $statistikQuery->whereMonth('tanggal_transaksi', now()->month)
                              ->whereYear('tanggal_transaksi', now()->year);
            }
            
            // Clone query untuk menghitung pemasukan dan pengeluaran
            $totalPemasukan = (clone $statistikQuery)->where('jenis_transaksi', 'pemasukan')->sum('nominal');
            $totalPengeluaran = (clone $statistikQuery)->where('jenis_transaksi', 'pengeluaran')->sum('nominal');
            
            // Saldo terakhir tetap dari data terbaru (tidak terpengaruh filter)
            $saldoTerakhir = $santri->saldo_uang_saku;
            
            // Info periode untuk ditampilkan di view
            if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
                $periodeTeks = 'Periode: ';
                if ($request->filled('tanggal_dari')) {
                    $periodeTeks .= \Carbon\Carbon::parse($request->tanggal_dari)->format('d/m/Y');
                }
                $periodeTeks .= ' - ';
                if ($request->filled('tanggal_sampai')) {
                    $periodeTeks .= \Carbon\Carbon::parse($request->tanggal_sampai)->format('d/m/Y');
                }
            } else {
                $periodeTeks = 'Bulan Ini: ' . now()->isoFormat('MMMM YYYY');
            }
            
            return view('santri.uang-saku.index', compact(
                'riwayatUangSaku',
                'santri',
                'totalPemasukan',
                'totalPengeluaran',
                'saldoTerakhir'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error di Riwayat Uang Saku Santri: ' . $e->getMessage());
            
            return back()->with('error', 'Terjadi kesalahan saat memuat riwayat uang saku');
        }
    }
    
    /**
     * Tampilkan detail transaksi
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            // Ambil data santri
            $santri = Santri::where('id_santri', $user->role_id)->first();
            
            if (!$santri) {
                abort(404, 'Data santri tidak ditemukan');
            }
            
            // Ambil transaksi dengan validasi kepemilikan
            $transaksi = UangSaku::where('id', $id)
                ->where('id_santri', $santri->id_santri)
                ->with('santri:id_santri,nama_lengkap,kelas')
                ->firstOrFail();
            
            return view('santri.uang-saku.show', compact('transaksi', 'santri'));
            
        } catch (\Exception $e) {
            Log::error('Error di Detail Uang Saku: ' . $e->getMessage());
            
            return back()->with('error', 'Transaksi tidak ditemukan atau Anda tidak memiliki akses');
        }
    }
}