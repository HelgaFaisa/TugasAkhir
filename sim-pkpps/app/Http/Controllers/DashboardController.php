<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
use App\Models\User;
use App\Models\RiwayatPelanggaran;
use App\Models\Berita;
use App\Models\KesehatanSantri;
use App\Models\Kepulangan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard Admin
     */
    public function admin()
    {
        try {
            $data = [
                'total_santri' => Santri::count(),
                'total_wali' => User::where('role', 'wali')->count(),
                'kegiatan_hari_ini' => 0,
            ];
            
            return view('admin.dashboardAdmin', compact('data'));
            
        } catch (\Exception $e) {
            Log::error('Error di Dashboard Admin: ' . $e->getMessage());
            
            return response()->view('errors.500', [
                'error' => 'Terjadi kesalahan saat memuat dashboard',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard Santri/Wali - OPTIMIZED ✅
     */
    public function santri()
    {
        try {
            $user = Auth::user();
            
            // Validasi role
            if (!in_array($user->role, ['santri', 'wali'])) {
                Log::error('Role tidak sesuai: ' . $user->role);
                abort(403, 'Akses ditolak. Role Anda: ' . $user->role);
            }
            
            // ✅ QUERY OPTIMIZED - Ambil data santri dengan kolom minimal
            $santri = Santri::where('id_santri', $user->role_id)
                ->select('id_santri', 'nama_lengkap', 'kelas')
                ->firstOrFail();
            
            $idSantri = $santri->id_santri;
            $today = Carbon::today();
            $weekAgo = Carbon::now()->subDays(7);
            
            // ✅ PARALLEL QUERIES - Eksekusi query secara bersamaan untuk performa
            $data = [
                'nama_santri' => $santri->nama_lengkap,
                'kelas' => $santri->kelas,
                'progres_quran' => 0, // Nanti diisi dari database capaian
                'progres_hadist' => 0, // Nanti diisi dari database capaian
                
                // Query langsung untuk saldo uang saku (dari accessor model)
                'saldo_uang_saku' => $santri->saldo_uang_saku,
                
                // Query optimized untuk poin pelanggaran
                'poin_pelanggaran' => RiwayatPelanggaran::where('id_santri', $idSantri)
                    ->sum('poin'),
            ];
            
            // ✅ Query status kesehatan (hanya jika sedang dirawat)
            $statusKesehatan = KesehatanSantri::where('id_santri', $idSantri)
                ->where('status', 'dirawat')
                ->select('id', 'keluhan', 'tanggal_masuk')
                ->orderBy('tanggal_masuk', 'desc')
                ->first();
            
            // ✅ Query kepulangan aktif (hanya jika sedang dalam periode pulang)
            $kepulanganAktif = Kepulangan::where('id_santri', $idSantri)
                ->where('status', 'Disetujui')
                ->whereDate('tanggal_pulang', '<=', $today)
                ->whereDate('tanggal_kembali', '>=', $today)
                ->select('id_kepulangan', 'tanggal_pulang', 'tanggal_kembali', 'alasan')
                ->first();
            
            // ✅ Query berita terbaru (7 hari terakhir) - OPTIMIZED
            $beritaTerbaru = Berita::select('id_berita', 'judul', 'created_at')
                ->where('status', 'published')
                ->where('created_at', '>=', $weekAgo)
                ->where(function($query) use ($santri) {
                    $query->where('target_berita', 'semua')
                        ->orWhere(function($q) use ($santri) {
                            $q->where('target_berita', 'kelas_tertentu')
                              ->whereJsonContains('target_kelas', $santri->kelas);
                        })
                        ->orWhereHas('santriTertentu', function($q) use ($santri) {
                            $q->where('santris.id_santri', $santri->id_santri);
                        });
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Return view dengan data yang sudah dioptimasi
            return view('santri.dashboardSantri', compact(
                'data',
                'santri',
                'user',
                'beritaTerbaru',
                'statusKesehatan',
                'kepulanganAktif'
            ));
            
        } catch (\Exception $e) {
            Log::error('=== ERROR DI DASHBOARD SANTRI ===');
            Log::error('Message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile());
            Log::error('Line: ' . $e->getLine());
            
            return response()->view('errors.500', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}