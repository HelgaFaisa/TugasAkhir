<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Santri;
use App\Models\User;
use App\Models\RiwayatPelanggaran;
use App\Models\Berita;
use App\Models\KesehatanSantri;
use App\Models\Kepulangan;
use App\Models\Capaian;
use App\Models\Semester;
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
            abort(500, 'Terjadi kesalahan saat memuat dashboard Admin: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard Santri/Wali - FIXED VERSION ✅
     */
    public function santri()
    {
        try {
            $user = Auth::user();
            
            Log::info('=== DASHBOARD SANTRI START ===');
            Log::info('User ID: ' . $user->id);
            Log::info('Role: ' . $user->role);
            Log::info('Role ID: ' . $user->role_id);
            
            // Validasi role
            if (!in_array($user->role, ['santri', 'wali'])) {
                Log::error('Role tidak sesuai: ' . $user->role);
                abort(403, 'Akses ditolak. Role Anda: ' . $user->role);
            }
            
            // ✅ Ambil data santri
            $santri = Santri::where('id_santri', $user->role_id)
                ->select('id_santri', 'nama_lengkap', 'kelas')
                ->first();
            
            if (!$santri) {
                Log::error('Santri tidak ditemukan dengan role_id: ' . $user->role_id);
                abort(404, 'Data santri tidak ditemukan.');
            }
            
            Log::info('Santri ditemukan: ' . $santri->nama_lengkap);
            
            $idSantri = $santri->id_santri;
            $today = Carbon::today();
            $weekAgo = Carbon::now()->subDays(7);
            
            // ✅ Ambil semester aktif dengan FALLBACK
            $semesterAktif = null;
            try {
                $semesterAktif = Semester::aktif()
                    ->select('id_semester', 'nama_semester', 'tahun_ajaran')
                    ->first();
                
                if (!$semesterAktif) {
                    $semesterAktif = Semester::select('id_semester', 'nama_semester', 'tahun_ajaran')
                        ->orderBy('tahun_ajaran', 'desc')
                        ->orderBy('periode', 'desc')
                        ->first();
                }
                
                Log::info('Semester aktif: ' . ($semesterAktif ? $semesterAktif->nama_semester : 'Tidak ada'));
            } catch (\Exception $e) {
                Log::warning('Error mengambil semester: ' . $e->getMessage());
                $semesterAktif = null;
            }
            
            // ✅ AMBIL PROGRES AL-QUR'AN dengan FALLBACK
            $progresAlquran = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                
                $progresAlquran = $query->whereHas('materi', function($q) {
                    $q->where('kategori', 'Al-Qur\'an');
                })->avg('persentase') ?? 0;
                
                Log::info('Progres Al-Quran: ' . $progresAlquran);
            } catch (\Exception $e) {
                Log::warning('Error progres Al-Quran: ' . $e->getMessage());
                $progresAlquran = 0;
            }
            
            // ✅ AMBIL PROGRES HADIST dengan FALLBACK
            $progresHadist = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                
                $progresHadist = $query->whereHas('materi', function($q) {
                    $q->where('kategori', 'Hadist');
                })->avg('persentase') ?? 0;
                
                Log::info('Progres Hadist: ' . $progresHadist);
            } catch (\Exception $e) {
                Log::warning('Error progres Hadist: ' . $e->getMessage());
                $progresHadist = 0;
            }
            
            // ✅ AMBIL PROGRES MATERI TAMBAHAN dengan FALLBACK
            $progresMateriTambahan = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                
                $progresMateriTambahan = $query->whereHas('materi', function($q) {
                    $q->where('kategori', 'Materi Tambahan');
                })->avg('persentase') ?? 0;
                
                Log::info('Progres Materi Tambahan: ' . $progresMateriTambahan);
            } catch (\Exception $e) {
                Log::warning('Error progres Materi Tambahan: ' . $e->getMessage());
                $progresMateriTambahan = 0;
            }
            
            // ✅ DATA UNTUK GRAFIK 1: Progress per Materi dengan FALLBACK
            $capaianPerMateri = collect([]);
            try {
                $query = Capaian::with(['materi' => function($q) {
                        $q->select('id_materi', 'nama_kitab', 'kategori', 'total_halaman');
                    }])
                    ->where('id_santri', $idSantri);
                
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                
                $capaianPerMateri = $query->select('id', 'id_materi', 'persentase', 'halaman_selesai')
                    ->orderBy('persentase', 'desc')
                    ->limit(10)
                    ->get();
                
                Log::info('Capaian per materi: ' . $capaianPerMateri->count() . ' items');
            } catch (\Exception $e) {
                Log::warning('Error capaian per materi: ' . $e->getMessage());
                $capaianPerMateri = collect([]);
            }
            
            // ✅ DATA UNTUK GRAFIK 2: Distribusi Status dengan FALLBACK
            $distribusiStatus = [
                'selesai' => 0,
                'hampir_selesai' => 0,
                'sedang_berjalan' => 0,
                'baru_dimulai' => 0,
            ];
            
            try {
                $baseQuery = Capaian::where('id_santri', $idSantri);
                
                if ($semesterAktif) {
                    $baseQuery->where('id_semester', $semesterAktif->id_semester);
                }
                
                $distribusiStatus = [
                    'selesai' => (clone $baseQuery)->where('persentase', '>=', 100)->count(),
                    'hampir_selesai' => (clone $baseQuery)->whereBetween('persentase', [75, 99.99])->count(),
                    'sedang_berjalan' => (clone $baseQuery)->whereBetween('persentase', [25, 74.99])->count(),
                    'baru_dimulai' => (clone $baseQuery)->whereBetween('persentase', [0, 24.99])->count(),
                ];
                
                Log::info('Distribusi status: ' . json_encode($distribusiStatus));
            } catch (\Exception $e) {
                Log::warning('Error distribusi status: ' . $e->getMessage());
            }
            
            // ✅ Data dashboard utama
            $data = [
                'nama_santri' => $santri->nama_lengkap,
                'kelas' => $santri->kelas,
                'progres_quran' => round($progresAlquran, 1),
                'progres_hadist' => round($progresHadist, 1),
                'progres_materi_tambahan' => round($progresMateriTambahan, 1),
                'saldo_uang_saku' => method_exists($santri, 'getSaldoUangSakuAttribute') 
                    ? $santri->saldo_uang_saku 
                    : 0,
                'poin_pelanggaran' => RiwayatPelanggaran::where('id_santri', $idSantri)->sum('poin') ?? 0,
            ];
            
            Log::info('Data array: ' . json_encode($data));
            
            // ✅ Query status kesehatan dengan FALLBACK
            $statusKesehatan = null;
            try {
                $statusKesehatan = KesehatanSantri::where('id_santri', $idSantri)
                    ->where('status', 'dirawat')
                    ->select('id', 'keluhan', 'tanggal_masuk')
                    ->orderBy('tanggal_masuk', 'desc')
                    ->first();
            } catch (\Exception $e) {
                Log::warning('Error status kesehatan: ' . $e->getMessage());
            }
            
            // ✅ Query kepulangan aktif dengan FALLBACK
            $kepulanganAktif = null;
            try {
                $kepulanganAktif = Kepulangan::where('id_santri', $idSantri)
                    ->where('status', 'Disetujui')
                    ->whereDate('tanggal_pulang', '<=', $today)
                    ->whereDate('tanggal_kembali', '>=', $today)
                    ->select('id_kepulangan', 'tanggal_pulang', 'tanggal_kembali', 'alasan')
                    ->first();
            } catch (\Exception $e) {
                Log::warning('Error kepulangan aktif: ' . $e->getMessage());
            }
            
            // ✅ Query berita terbaru dengan FALLBACK
            $beritaTerbaru = collect([]);
            try {
                $beritaTerbaru = Berita::select('id_berita', 'judul', 'created_at')
                    ->where('status', 'published')
                    ->where('created_at', '>=', $weekAgo)
                    ->where(function($query) use ($santri) {
                        $query->where('target_berita', 'semua')
                            ->orWhere(function($q) use ($santri) {
                                $q->where('target_berita', 'kelas_tertentu')
                                  ->whereJsonContains('target_kelas', $santri->kelas);
                            });
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                
                Log::info('Berita terbaru: ' . $beritaTerbaru->count() . ' items');
            } catch (\Exception $e) {
                Log::warning('Error berita terbaru: ' . $e->getMessage());
                $beritaTerbaru = collect([]);
            }
            
            Log::info('=== DASHBOARD SANTRI SUCCESS ===');
            
            // Return view dengan semua data
            return view('santri.dashboardSantri', compact(
                'data',
                'santri',
                'user',
                'beritaTerbaru',
                'statusKesehatan',
                'kepulanganAktif',
                'capaianPerMateri',
                'distribusiStatus',
                'semesterAktif'
            ));
            
        } catch (\Exception $e) {
            Log::error('=== FATAL ERROR DI DASHBOARD SANTRI ===');
            Log::error('Message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile());
            Log::error('Line: ' . $e->getLine());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            // Tampilkan error detail jika debug mode
            if (config('app.debug')) {
                abort(500, 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            } else {
                abort(500, 'Terjadi kesalahan saat memuat dashboard. Silakan hubungi administrator.');
            }
        }
    }
}