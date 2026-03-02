<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Santri;
use App\Models\SantriKelas;
use App\Models\User;
use App\Models\Kegiatan;
use App\Models\AbsensiKegiatan;
use App\Models\KategoriKegiatan;
use App\Models\RiwayatPelanggaran;
use App\Models\Berita;
use App\Models\KesehatanSantri;
use App\Models\Kepulangan;
use App\Models\PengajuanKepulangan;
use App\Models\PembayaranSpp;
use App\Models\Keuangan;          // ← TAMBAHAN: untuk data kas pondok
use App\Models\UangSaku;
use App\Models\Capaian;
use App\Models\Semester;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mapping hari Carbon (English) → DB enum (Indonesia)
     */
    private function hariIndonesia(): array
    {
        return [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
            'Sunday' => 'Ahad',
        ];
    }

    /**
     * Dashboard Admin
     */
    public function admin()
    {
        try {
            $today = Carbon::today();
            $now = Carbon::now();
            $hariIni = $this->hariIndonesia()[$today->format('l')];
            $bulanIni = (int) $today->format('m');
            $tahunIni = (int) $today->format('Y');

            // ────────────────────────── KPI CARDS ──────────────────────────
            $user = Auth::user();
            $totalSantriAktif = Cache::remember('dash_santri_aktif', 300, function () {
                return Santri::aktif()->count();
            });

            // Kegiatan hari ini + status absensi
            $kegiatanHariIni = Kegiatan::with(['kategori', 'absensis' => function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            }])
                ->where('hari', $hariIni)
                ->orderBy('waktu_mulai')
                ->get();

            $totalKegiatan = $kegiatanHariIni->count();
            $sudahAbsensi = $kegiatanHariIni->filter(function ($k) {
                return $k->absensis->isNotEmpty();
            })->count();
            $belumAbsensi = $totalKegiatan - $sudahAbsensi;

            // Santri di UKP (sedang dirawat)
            $santriSakit = KesehatanSantri::dirawat()->count();

            // Pengajuan kepulangan menunggu approval
            $kepulanganMenunggu = PengajuanKepulangan::where('status', 'Menunggu')->count();

            // Santri aktif yang belum punya akun wali (super_admin only)
            $santriTanpaWali = 0;
            if ($user->role === 'super_admin') {
                $santriTanpaWali = Santri::aktif()
                    ->whereDoesntHave('waliUser')
                    ->count();
            }

            $kpiCards = compact(
                'totalSantriAktif', 'totalKegiatan', 'sudahAbsensi',
                'belumAbsensi', 'santriSakit', 'kepulanganMenunggu', 'santriTanpaWali'
            );

            // ──────────────────── JADWAL KEGIATAN HARI INI ────────────────────
            $kegiatanHariIni->each(function ($kegiatan) use ($now, $today, $totalSantriAktif) {
                $waktuMulaiStr = is_string($kegiatan->waktu_mulai) ? $kegiatan->waktu_mulai : $kegiatan->waktu_mulai->format('H:i');
                $waktuSelesaiStr = is_string($kegiatan->waktu_selesai) ? $kegiatan->waktu_selesai : $kegiatan->waktu_selesai->format('H:i');

                $mulai = Carbon::parse($today->format('Y-m-d') . ' ' . $waktuMulaiStr);
                $selesai = Carbon::parse($today->format('Y-m-d') . ' ' . $waktuSelesaiStr);

                $kegiatan->status_kegiatan = $now->lt($mulai) ? 'belum'
                    : ($now->between($mulai, $selesai) ? 'berlangsung' : 'selesai');

                $totalAbsen = $kegiatan->absensis->count();
                $hadir = $kegiatan->absensis->where('status', 'Hadir')->count();
                $kegiatan->persen_kehadiran = $totalAbsen > 0 ? round(($hadir / $totalAbsen) * 100) : 0;
                $kegiatan->total_absensi = $totalAbsen;
                $kegiatan->belum_input = $kegiatan->status_kegiatan === 'selesai' && $totalAbsen === 0;
            });

            // ────────────────────────── ALERT PANEL ──────────────────────────
            // 1) Santri alpa beruntun (semua role bisa lihat)
            $santriAlpaBeruntun = $this->getSantriAlpaBeruntun();

            // 2) SPP jatuh tempo (super_admin only)
            $sppJatuhTempo = collect([]);
            if ($user->role === 'super_admin') {
                $sppJatuhTempo = PembayaranSpp::telat()
                    ->with('santri:id_santri,nama_lengkap')
                    ->select('id_pembayaran', 'id_santri', 'bulan', 'tahun', 'nominal', 'batas_bayar')
                    ->orderBy('batas_bayar')
                    ->limit(10)
                    ->get();
            }

            // 3) Pengajuan kepulangan menunggu review
            $kepulanganPending = PengajuanKepulangan::where('status', 'Menunggu')
                ->with('santri:id_santri,nama_lengkap')
                ->select('id_pengajuan', 'id_santri', 'tanggal_pulang', 'tanggal_kembali', 'alasan')
                ->orderBy('created_at')
                ->limit(5)
                ->get();

            $alerts = compact('santriAlpaBeruntun', 'sppJatuhTempo', 'kepulanganPending');

            // ──────────────── GRAFIK TREN KEHADIRAN (4 MINGGU) ────────────────
            $trenKehadiran = $this->getTrenKehadiran($today);

            // ──────────────── RINGKASAN SPP + KEUANGAN BULAN INI ─────────────
            // Default (untuk non super_admin atau jika query gagal)
            $sppBulanIni = [
                'lunas'         => 0,
                'belum'         => 0,
                'terkumpul'     => 0,
                'totalTagihan'  => 0,
                'pemasukanLain' => 0,  // pemasukan kas pondok selain SPP
                'pengeluaran'   => 0,  // pengeluaran kas pondok
            ];

            if ($user->role === 'super_admin') {
                // Pakai cache key baru "dash_spp_full_" agar tidak tumpang-tindih
                // dengan cache key lama "dash_spp_" yang belum punya key keuangan
                $sppBulanIni = Cache::remember("dash_spp_full_{$bulanIni}_{$tahunIni}", 300, function () use ($bulanIni, $tahunIni) {
                    // ── Data SPP ──
                    $lunas        = PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->lunas()->count();
                    $belum        = PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->belumLunas()->count();
                    $terkumpul    = (float) PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->lunas()->sum('nominal');
                    $totalTagihan = (float) PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->sum('nominal');

                    // ── Data Keuangan Pondok (non-SPP) ──
                    $pemasukanLain = (float) Keuangan::pemasukan()
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->sum('nominal');

                    $pengeluaran = (float) Keuangan::pengeluaran()
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->sum('nominal');

                    return compact('lunas', 'belum', 'terkumpul', 'totalTagihan', 'pemasukanLain', 'pengeluaran');
                });
            }

            return view('admin.dashboardAdmin', compact(
                'kpiCards', 'kegiatanHariIni', 'alerts',
                'trenKehadiran', 'sppBulanIni',
                'hariIni', 'today'
            ));

        } catch (\Exception $e) {
            Log::error('Error di Dashboard Admin: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            if (config('app.debug')) {
                abort(500, 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            }
            abort(500, 'Terjadi kesalahan saat memuat dashboard Admin.');
        }
    }

    // ══════════════════ HELPER METHODS ══════════════════

    /**
     * Santri dengan alpa ≥ 3x beruntun dalam 7 hari terakhir
     */
    private function getSantriAlpaBeruntun(int $threshold = 3): \Illuminate\Support\Collection
    {
        $weekAgo = Carbon::today()->subDays(7);

        $alpaData = AbsensiKegiatan::where('status', 'Alpa')
            ->whereDate('tanggal', '>=', $weekAgo)
            ->select('id_santri')
            ->selectRaw('COUNT(*) as total_alpa')
            ->groupBy('id_santri')
            ->having('total_alpa', '>=', $threshold)
            ->pluck('total_alpa', 'id_santri');

        if ($alpaData->isEmpty()) {
            return collect([]);
        }

        return Santri::aktif()
            ->whereIn('id_santri', $alpaData->keys())
            ->select('id_santri', 'nama_lengkap')
            ->get()
            ->map(fn ($s) => (object) [
                'nama' => $s->nama_lengkap,
                'id_santri' => $s->id_santri,
                'total_alpa' => $alpaData[$s->id_santri],
            ]);
    }

    /**
     * Tren kehadiran 4 minggu terakhir, dikelompokkan per kategori kegiatan
     */
    private function getTrenKehadiran(Carbon $today): array
    {
        $labels = [];
        $series = [];

        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();

        for ($i = 3; $i >= 0; $i--) {
            $start = $today->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $end = $start->copy()->endOfWeek(Carbon::SUNDAY);
            $labels[] = 'Mg ' . (4 - $i);

            foreach ($kategoris as $kat) {
                $kegiatanIds = Kegiatan::where('kategori_id', $kat->kategori_id)
                    ->pluck('kegiatan_id');

                $totalAbsen = AbsensiKegiatan::whereIn('kegiatan_id', $kegiatanIds)
                    ->dateRange($start, $end)
                    ->count();
                $hadir = AbsensiKegiatan::whereIn('kegiatan_id', $kegiatanIds)
                    ->dateRange($start, $end)
                    ->where('status', 'Hadir')
                    ->count();

                $series[$kat->nama_kategori][] = $totalAbsen > 0 ? round(($hadir / $totalAbsen) * 100, 1) : 0;
            }
        }

        return compact('labels', 'series');
    }

    /**
     * Feed aktivitas terbaru
     */
    private function getFeedAktivitas(Carbon $today): \Illuminate\Support\Collection
    {
        $items = collect();

        AbsensiKegiatan::with(['santri:id_santri,nama_lengkap', 'kegiatan:kegiatan_id,nama_kegiatan'])
            ->whereDate('tanggal', $today)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->each(fn ($a) => $items->push((object) [
                'icon' => 'fa-clipboard-check',
                'color' => 'success',
                'text' => ($a->santri->nama_lengkap ?? '-') . ' — ' . $a->status . ' di ' . ($a->kegiatan->nama_kegiatan ?? '-'),
                'time' => $a->created_at,
            ]));

        RiwayatPelanggaran::with(['santri:id_santri,nama_lengkap', 'kategori:id_kategori,nama_pelanggaran'])
            ->whereDate('tanggal', '>=', $today->copy()->subDays(7))
            ->terbaru()
            ->limit(5)
            ->get()
            ->each(fn ($p) => $items->push((object) [
                'icon' => 'fa-exclamation-triangle',
                'color' => 'danger',
                'text' => ($p->santri->nama_lengkap ?? '-') . ' — ' . ($p->kategori->nama_pelanggaran ?? '-') . ' (' . $p->poin . ' poin)',
                'time' => $p->created_at,
            ]));

        PembayaranSpp::with('santri:id_santri,nama_lengkap')
            ->lunas()
            ->whereNotNull('tanggal_bayar')
            ->whereDate('tanggal_bayar', '>=', $today->copy()->subDays(7))
            ->orderByDesc('tanggal_bayar')
            ->limit(5)
            ->get()
            ->each(fn ($s) => $items->push((object) [
                'icon' => 'fa-money-bill-wave',
                'color' => 'info',
                'text' => ($s->santri->nama_lengkap ?? '-') . ' — SPP ' . $s->bulan_nama . '/' . $s->tahun . ' (Rp ' . number_format($s->nominal, 0, ',', '.') . ')',
                'time' => $s->created_at,
            ]));

        return $items->sortByDesc('time')->take(10)->values();
    }

    /**
     * Dashboard Santri
     */
    public function santri()
    {
        try {
            $account = auth('santri')->user();

            Log::info('=== DASHBOARD SANTRI START ===');
            Log::info('Account ID: ' . $account->id);
            Log::info('Role: ' . $account->role);
            Log::info('ID Santri: ' . $account->id_santri);

            $santri = Santri::with([
                    'kelasPrimary.kelas.kelompok',
                ])
                ->where('id_santri', $account->id_santri)
                ->select('id_santri', 'nama_lengkap')
                ->first();

            if (!$santri) {
                Log::error('Santri tidak ditemukan dengan id_santri: ' . $account->id_santri);
                abort(404, 'Data santri tidak ditemukan.');
            }

            Log::info('Santri ditemukan: ' . $santri->nama_lengkap);

            $namaKelas = $santri->kelas;
            $idSantri = $santri->id_santri;
            $today = Carbon::today();
            $weekAgo = Carbon::now()->subDays(7);

            // Ambil semester aktif dengan FALLBACK
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

            // Progres Al-Qur'an
            $progresAlquran = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                $progresAlquran = $query->whereHas('materi', function ($q) {
                    $q->where('kategori', 'Al-Qur\'an');
                })->avg('persentase') ?? 0;

                Log::info('Progres Al-Quran: ' . $progresAlquran);
            } catch (\Exception $e) {
                Log::warning('Error progres Al-Quran: ' . $e->getMessage());
            }

            // Progres Hadist
            $progresHadist = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                $progresHadist = $query->whereHas('materi', function ($q) {
                    $q->where('kategori', 'Hadist');
                })->avg('persentase') ?? 0;

                Log::info('Progres Hadist: ' . $progresHadist);
            } catch (\Exception $e) {
                Log::warning('Error progres Hadist: ' . $e->getMessage());
            }

            // Progres Materi Tambahan
            $progresMateriTambahan = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                $progresMateriTambahan = $query->whereHas('materi', function ($q) {
                    $q->where('kategori', 'Materi Tambahan');
                })->avg('persentase') ?? 0;

                Log::info('Progres Materi Tambahan: ' . $progresMateriTambahan);
            } catch (\Exception $e) {
                Log::warning('Error progres Materi Tambahan: ' . $e->getMessage());
            }

            // Data untuk grafik: Progress per Materi
            $capaianPerMateri = collect([]);
            try {
                $query = Capaian::with(['materi' => function ($q) {
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

            // Data untuk grafik: Distribusi Status
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

            $data = [
                'nama_santri'             => $santri->nama_lengkap,
                'kelas'                   => $namaKelas,
                'progres_quran'           => round($progresAlquran, 1),
                'progres_hadist'          => round($progresHadist, 1),
                'progres_materi_tambahan' => round($progresMateriTambahan, 1),
                'saldo_uang_saku'         => $santri->saldo_uang_saku ?? 0,
                'poin_pelanggaran'        => RiwayatPelanggaran::where('id_santri', $idSantri)->sum('poin') ?? 0,
            ];

            Log::info('Data array: ' . json_encode($data));

            // Status kesehatan
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

            // Kepulangan aktif
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

            // Berita terbaru
            $beritaTerbaru = collect([]);
            try {
                $beritaTerbaru = Berita::select('id_berita', 'judul', 'created_at')
                    ->where('status', 'published')
                    ->where('created_at', '>=', $weekAgo)
                    ->where(function ($query) use ($namaKelas) {
                        $query->where('target_berita', 'semua')
                            ->orWhere(function ($q) use ($namaKelas) {
                                $q->where('target_berita', 'kelas_tertentu')
                                  ->whereJsonContains('target_kelas', $namaKelas);
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

            return view('santri.dashboardSantri', compact(
                'data',
                'santri',
                'account',
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

            if (config('app.debug')) {
                abort(500, 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            }
            abort(500, 'Terjadi kesalahan saat memuat dashboard. Silakan hubungi administrator.');
        }
    }
}