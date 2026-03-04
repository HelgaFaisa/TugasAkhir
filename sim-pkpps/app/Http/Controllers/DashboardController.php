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
use App\Models\Keuangan;
use App\Models\UangSaku;
use App\Models\Capaian;
use App\Models\Semester;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mapping hari Carbon (English) -> DB enum (Indonesia)
     */
    private function hariIndonesia(): array
    {
        return [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Ahad',
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    //  DASHBOARD ADMIN  —  tidak ada perubahan
    // ══════════════════════════════════════════════════════════════════
    public function admin()
    {
        try {
            $today      = Carbon::today();
            $now        = Carbon::now();
            $hariIni    = $this->hariIndonesia()[$today->format('l')];
            $bulanIni   = (int) $today->format('m');
            $tahunIni   = (int) $today->format('Y');

            // KPI CARDS
            $user = Auth::user();

            $totalSantriAktif = Cache::remember('dash_santri_aktif', 300, function () {
                return Santri::aktif()->count();
            });

            $kegiatanHariIni = Kegiatan::with(['kategori', 'absensis' => function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            }])
                ->where('hari', $hariIni)
                ->orderBy('waktu_mulai')
                ->get();

            $totalKegiatan  = $kegiatanHariIni->count();
            $sudahAbsensi   = $kegiatanHariIni->filter(fn($k) => $k->absensis->isNotEmpty())->count();
            $belumAbsensi   = $totalKegiatan - $sudahAbsensi;

            $santriSakit        = KesehatanSantri::dirawat()->count();
            $kepulanganMenunggu = PengajuanKepulangan::where('status', 'Menunggu')->count();

            $santriTanpaWali = 0;
            if ($user->role === 'super_admin') {
                $santriTanpaWali = Santri::aktif()->whereDoesntHave('waliUser')->count();
            }

            $kpiCards = compact(
                'totalSantriAktif', 'totalKegiatan', 'sudahAbsensi',
                'belumAbsensi', 'santriSakit', 'kepulanganMenunggu', 'santriTanpaWali'
            );

            // JADWAL KEGIATAN HARI INI
            $kegiatanHariIni->each(function ($kegiatan) use ($now, $today, $totalSantriAktif) {
                $waktuMulaiStr   = is_string($kegiatan->waktu_mulai)   ? $kegiatan->waktu_mulai   : $kegiatan->waktu_mulai->format('H:i');
                $waktuSelesaiStr = is_string($kegiatan->waktu_selesai) ? $kegiatan->waktu_selesai : $kegiatan->waktu_selesai->format('H:i');

                $mulai   = Carbon::parse($today->format('Y-m-d') . ' ' . $waktuMulaiStr);
                $selesai = Carbon::parse($today->format('Y-m-d') . ' ' . $waktuSelesaiStr);

                $kegiatan->status_kegiatan = $now->lt($mulai) ? 'belum'
                    : ($now->between($mulai, $selesai) ? 'berlangsung' : 'selesai');

                $totalAbsen = $kegiatan->absensis->count();
                $hadir      = $kegiatan->absensis->where('status', 'Hadir')->count();

                $kegiatan->persen_kehadiran = $totalAbsen > 0 ? round(($hadir / $totalAbsen) * 100) : 0;
                $kegiatan->total_absensi    = $totalAbsen;
                $kegiatan->belum_input      = $kegiatan->status_kegiatan === 'selesai' && $totalAbsen === 0;
            });

            // ALERT PANEL
            $santriAlpaBeruntun = $this->getSantriAlpaBeruntun();

            $sppJatuhTempo = collect([]);
            if ($user->role === 'super_admin') {
                $sppJatuhTempo = PembayaranSpp::telat()
                    ->with('santri:id_santri,nama_lengkap')
                    ->select('id_pembayaran', 'id_santri', 'bulan', 'tahun', 'nominal', 'batas_bayar')
                    ->orderBy('batas_bayar')
                    ->limit(10)
                    ->get();
            }

            $kepulanganPending = PengajuanKepulangan::where('status', 'Menunggu')
                ->with('santri:id_santri,nama_lengkap')
                ->select('id_pengajuan', 'id_santri', 'tanggal_pulang', 'tanggal_kembali', 'alasan')
                ->orderBy('created_at')
                ->limit(5)
                ->get();

            $alerts = compact('santriAlpaBeruntun', 'sppJatuhTempo', 'kepulanganPending');

            // GRAFIK TREN KEHADIRAN (4 MINGGU)
            $trenKehadiran = $this->getTrenKehadiran($today);

            // RINGKASAN SPP + KEUANGAN BULAN INI
            $sppBulanIni = [
                'lunas'         => 0,
                'belum'         => 0,
                'terkumpul'     => 0,
                'totalTagihan'  => 0,
                'pemasukanLain' => 0,
                'pengeluaran'   => 0,
            ];

            if ($user->role === 'super_admin') {
                $sppBulanIni = Cache::remember("dash_spp_full_{$bulanIni}_{$tahunIni}", 300, function () use ($bulanIni, $tahunIni) {
                    $lunas        = PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->lunas()->count();
                    $belum        = PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->belumLunas()->count();
                    $terkumpul    = (float) PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->lunas()->sum('nominal');
                    $totalTagihan = (float) PembayaranSpp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->sum('nominal');

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

    // ══════════════════════════════════════════════════════════════════
    //  HELPER METHODS  (dipakai oleh admin)
    // ══════════════════════════════════════════════════════════════════

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
            ->map(fn($s) => (object) [
                'nama'       => $s->nama_lengkap,
                'id_santri'  => $s->id_santri,
                'total_alpa' => $alpaData[$s->id_santri],
            ]);
    }

    private function getTrenKehadiran(Carbon $today): array
    {
        $labels = [];
        $series = [];

        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();

        for ($i = 3; $i >= 0; $i--) {
            $start    = $today->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $end      = $start->copy()->endOfWeek(Carbon::SUNDAY);
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

                $series[$kat->nama_kategori][] = $totalAbsen > 0
                    ? round(($hadir / $totalAbsen) * 100, 1)
                    : 0;
            }
        }

        return compact('labels', 'series');
    }

    private function getFeedAktivitas(Carbon $today): \Illuminate\Support\Collection
    {
        $items = collect();

        AbsensiKegiatan::with(['santri:id_santri,nama_lengkap', 'kegiatan:kegiatan_id,nama_kegiatan'])
            ->whereDate('tanggal', $today)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->each(fn($a) => $items->push((object) [
                'icon'  => 'fa-clipboard-check',
                'color' => 'success',
                'text'  => ($a->santri->nama_lengkap ?? '-') . ' — ' . $a->status . ' di ' . ($a->kegiatan->nama_kegiatan ?? '-'),
                'time'  => $a->created_at,
            ]));

        RiwayatPelanggaran::with(['santri:id_santri,nama_lengkap', 'kategori:id_kategori,nama_pelanggaran'])
            ->whereDate('tanggal', '>=', $today->copy()->subDays(7))
            ->terbaru()
            ->limit(5)
            ->get()
            ->each(fn($p) => $items->push((object) [
                'icon'  => 'fa-exclamation-triangle',
                'color' => 'danger',
                'text'  => ($p->santri->nama_lengkap ?? '-') . ' — ' . ($p->kategori->nama_pelanggaran ?? '-') . ' (' . $p->poin . ' poin)',
                'time'  => $p->created_at,
            ]));

        PembayaranSpp::with('santri:id_santri,nama_lengkap')
            ->lunas()
            ->whereNotNull('tanggal_bayar')
            ->whereDate('tanggal_bayar', '>=', $today->copy()->subDays(7))
            ->orderByDesc('tanggal_bayar')
            ->limit(5)
            ->get()
            ->each(fn($s) => $items->push((object) [
                'icon'  => 'fa-money-bill-wave',
                'color' => 'info',
                'text'  => ($s->santri->nama_lengkap ?? '-') . ' — SPP ' . $s->bulan_nama . '/' . $s->tahun . ' (Rp ' . number_format($s->nominal, 0, ',', '.') . ')',
                'time'  => $s->created_at,
            ]));

        return $items->sortByDesc('time')->take(10)->values();
    }

    // ══════════════════════════════════════════════════════════════════
    //  HELPER: Absensi per kategori (dipakai santri())
    // ══════════════════════════════════════════════════════════════════

    /**
     * Ambil statistik absensi per kategori kegiatan untuk 1 santri
     * dalam rentang tanggal tertentu.
     *
     * @param  int     $idSantri
     * @param  string  $dateStart  format Y-m-d
     * @param  string  $dateEnd    format Y-m-d
     * @return array   ['labels'=>[], 'hadir'=>[], 'alpa'=>[], 'izin'=>[], 'sakit'=>[]]
     */
    private function getAbsensiPerKategori(string|int $idSantri, string $dateStart, string $dateEnd): array
    {
        $result = ['labels' => [], 'hadir' => [], 'alpa' => [], 'izin' => [], 'sakit' => []];

        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        foreach ($kategoris as $kat) {
            $kegIds = Kegiatan::where('kategori_id', $kat->kategori_id)
                ->pluck('kegiatan_id');

            if ($kegIds->isEmpty()) {
                continue;
            }

            $abs = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereIn('kegiatan_id', $kegIds)
                ->whereBetween('tanggal', [$dateStart, $dateEnd])
                ->get();

            // Skip kategori yang tidak punya record sama sekali di periode ini
            if ($abs->isEmpty()) {
                continue;
            }

            $result['labels'][] = $kat->nama_kategori;
            $result['hadir'][]  = $abs->whereIn('status', ['Hadir', 'Terlambat'])->count();
            $result['alpa'][]   = $abs->where('status', 'Alpa')->count();
            $result['izin'][]   = $abs->where('status', 'Izin')->count();
            $result['sakit'][]  = $abs->where('status', 'Sakit')->count();
        }

        return $result;
    }

    // ══════════════════════════════════════════════════════════════════
    //  DASHBOARD SANTRI
    // ══════════════════════════════════════════════════════════════════
    public function santri()
    {
        try {
            $account = auth('santri')->user();

            Log::info('=== DASHBOARD SANTRI START ===');
            Log::info('Account ID: '  . $account->id);
            Log::info('Role: '        . $account->role);
            Log::info('ID Santri: '   . $account->id_santri);

            $santri = Santri::with(['kelasPrimary.kelas.kelompok'])
                ->where('id_santri', $account->id_santri)
                ->select('id_santri', 'nama_lengkap')
                ->first();

            if (!$santri) {
                Log::error('Santri tidak ditemukan dengan id_santri: ' . $account->id_santri);
                abort(404, 'Data santri tidak ditemukan.');
            }

            Log::info('Santri ditemukan: ' . $santri->nama_lengkap);

            $namaKelas = $santri->kelas;
            $idSantri  = $santri->id_santri;
            $today     = Carbon::today();

            // ─── Semester aktif ───────────────────────────────────────
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
            }

            // ─── Progres Al-Qur'an ────────────────────────────────────
            $progresAlquran = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                $progresAlquran = $query->whereHas('materi', fn($q) => $q->where('kategori', "Al-Qur'an"))
                    ->avg('persentase') ?? 0;
            } catch (\Exception $e) {
                Log::warning('Error progres Al-Quran: ' . $e->getMessage());
            }

            // ─── Progres Hadist ───────────────────────────────────────
            $progresHadist = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                $progresHadist = $query->whereHas('materi', fn($q) => $q->where('kategori', 'Hadist'))
                    ->avg('persentase') ?? 0;
            } catch (\Exception $e) {
                Log::warning('Error progres Hadist: ' . $e->getMessage());
            }

            // ─── Progres Materi Tambahan ──────────────────────────────
            $progresMateriTambahan = 0;
            try {
                $query = Capaian::where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                $progresMateriTambahan = $query->whereHas('materi', fn($q) => $q->where('kategori', 'Materi Tambahan'))
                    ->avg('persentase') ?? 0;
            } catch (\Exception $e) {
                Log::warning('Error progres Materi Tambahan: ' . $e->getMessage());
            }

            // ─── Capaian per Materi ───────────────────────────────────
            $capaianPerMateri = collect([]);
            try {
                $query = Capaian::with(['materi' => fn($q) => $q->select('id_materi', 'nama_kitab', 'kategori', 'total_halaman')])
                    ->where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $query->where('id_semester', $semesterAktif->id_semester);
                }
                $capaianPerMateri = $query->select('id', 'id_materi', 'persentase', 'halaman_selesai')
                    ->orderBy('persentase', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::warning('Error capaian per materi: ' . $e->getMessage());
            }

            // ─── Distribusi Status ────────────────────────────────────
            $distribusiStatus = ['selesai' => 0, 'hampir_selesai' => 0, 'sedang_berjalan' => 0, 'baru_dimulai' => 0];
            try {
                $baseQuery = Capaian::where('id_santri', $idSantri);
                if ($semesterAktif) {
                    $baseQuery->where('id_semester', $semesterAktif->id_semester);
                }
                $distribusiStatus = [
                    'selesai'         => (clone $baseQuery)->where('persentase', '>=', 100)->count(),
                    'hampir_selesai'  => (clone $baseQuery)->whereBetween('persentase', [75, 99.99])->count(),
                    'sedang_berjalan' => (clone $baseQuery)->whereBetween('persentase', [25, 74.99])->count(),
                    'baru_dimulai'    => (clone $baseQuery)->whereBetween('persentase', [0, 24.99])->count(),
                ];
            } catch (\Exception $e) {
                Log::warning('Error distribusi status: ' . $e->getMessage());
            }

            // ─── Status Kesehatan ─────────────────────────────────────
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

            // ─── Kepulangan Aktif ─────────────────────────────────────
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

            // ─── Berita Terbaru ───────────────────────────────────────
            // Tanpa filter tanggal agar semua berita relevan muncul, limit 5
            $beritaTerbaru = collect([]);
            try {
                $beritaTerbaru = Berita::select('id_berita', 'judul', 'created_at')
                    ->where('status', 'published')
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
            } catch (\Exception $e) {
                Log::warning('Error berita terbaru: ' . $e->getMessage());
            }

            // ─── Statistik Kepulangan Tahun Ini ──────────────────────
            $statistikKepulangan = [
                'total_hari'   => 0,
                'sisa_kuota'   => 12,
                'persen_kuota' => 0,
                'disetujui'    => 0,
                'menunggu'     => 0,
                'over_limit'   => false,
            ];
            try {
                $kepulanganTahunIni = Kepulangan::where('id_santri', $idSantri)
                    ->whereYear('tanggal_pulang', $today->year)
                    ->get();

                $totalHariKepulangan = $kepulanganTahunIni
                    ->whereIn('status', ['Disetujui', 'Selesai'])
                    ->sum('durasi_izin');

                $statistikKepulangan = [
                    'total_hari'   => $totalHariKepulangan,
                    'sisa_kuota'   => max(0, 12 - $totalHariKepulangan),
                    'persen_kuota' => min(100, round(($totalHariKepulangan / 12) * 100)),
                    'disetujui'    => $kepulanganTahunIni->whereIn('status', ['Disetujui', 'Selesai'])->count(),
                    'menunggu'     => $kepulanganTahunIni->where('status', 'Menunggu')->count(),
                    'over_limit'   => $totalHariKepulangan > 12,
                ];

                Log::info('Statistik kepulangan: ' . json_encode($statistikKepulangan));
            } catch (\Exception $e) {
                Log::warning('Error statistik kepulangan: ' . $e->getMessage());
            }

            // ─── Statistik Kesehatan Bulan Ini ───────────────────────
            $statistikKesehatan = [
                'total_kunjungan' => 0,
                'sembuh'          => 0,
                'dirawat'         => 0,
                'izin'            => 0,
            ];
            try {
                $kesehatanBulanIni = KesehatanSantri::where('id_santri', $idSantri)
                    ->whereMonth('tanggal_masuk', $today->month)
                    ->whereYear('tanggal_masuk', $today->year)
                    ->get();

                $statistikKesehatan = [
                    'total_kunjungan' => $kesehatanBulanIni->count(),
                    'sembuh'          => $kesehatanBulanIni->where('status', 'sembuh')->count(),
                    'dirawat'         => $kesehatanBulanIni->where('status', 'dirawat')->count(),
                    'izin'            => $kesehatanBulanIni->where('status', 'izin')->count(),
                ];
            } catch (\Exception $e) {
                Log::warning('Error statistik kesehatan: ' . $e->getMessage());
            }

            // ─── 5 Pelanggaran Terbaru ────────────────────────────────
            $pelanggaranTerbaru = collect([]);
            try {
                $pelanggaranTerbaru = RiwayatPelanggaran::with('kategori:id,id_kategori,nama_pelanggaran')
                    ->where('id_santri', $idSantri)
                    ->select('id', 'id_riwayat', 'id_kategori', 'tanggal', 'poin', 'keterangan')
                    ->orderBy('tanggal', 'desc')
                    ->limit(5)
                    ->get();

                Log::info('Pelanggaran terbaru: ' . $pelanggaranTerbaru->count() . ' items');
            } catch (\Exception $e) {
                Log::warning('Error pelanggaran terbaru: ' . $e->getMessage());
            }

            // ─── [BARU] Absensi per Kategori — Bulan Ini ─────────────
            $absensiPerKategori = ['labels' => [], 'hadir' => [], 'alpa' => [], 'izin' => [], 'sakit' => []];
            try {
                $startBulan = $today->copy()->startOfMonth()->format('Y-m-d');
                $endBulan   = $today->format('Y-m-d');

                $absensiPerKategori = $this->getAbsensiPerKategori($idSantri, $startBulan, $endBulan);

                Log::info('Absensi per kategori bulan ini: ' . count($absensiPerKategori['labels']) . ' kategori');
            } catch (\Exception $e) {
                Log::warning('Error absensi per kategori bulan: ' . $e->getMessage());
            }

            // ─── [BARU] Absensi per Kategori — Minggu Ini ────────────
            $absensiPerKategoriMinggu = ['labels' => [], 'hadir' => [], 'alpa' => [], 'izin' => [], 'sakit' => []];
            try {
                $startMinggu = $today->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
                $endMinggu   = $today->format('Y-m-d');

                $absensiPerKategoriMinggu = $this->getAbsensiPerKategori($idSantri, $startMinggu, $endMinggu);

                Log::info('Absensi per kategori minggu ini: ' . count($absensiPerKategoriMinggu['labels']) . ' kategori');
            } catch (\Exception $e) {
                Log::warning('Error absensi per kategori minggu: ' . $e->getMessage());
            }

            // ─── [BARU] Status Input Capaian ──────────────────────────
            $statusInputCapaian = [
                'is_open'      => false,
                'deadline'     => null,
                'sudah_input'  => 0,
                'total_materi' => 0,
            ];
            try {
                if ($semesterAktif) {
                    // Sesuaikan nama kolom jika berbeda di tabel semesters
                    $bukaSemester  = $semesterAktif->tanggal_buka_input  ?? null;
                    $tutupSemester = $semesterAktif->tanggal_tutup_input ?? null;

                    $isOpen = false;
                    if ($bukaSemester && $tutupSemester) {
                        $now = Carbon::now();
                        $isOpen = $now->gte(Carbon::parse($bukaSemester))
                               && $now->lte(Carbon::parse($tutupSemester));
                    }

                    $sudahInput  = Capaian::where('id_santri', $idSantri)
                        ->where('id_semester', $semesterAktif->id_semester)
                        ->where('persentase', '>', 0)
                        ->count();

                    $totalMateri = Capaian::where('id_santri', $idSantri)
                        ->where('id_semester', $semesterAktif->id_semester)
                        ->count();

                    $statusInputCapaian = [
                        'is_open'      => $isOpen,
                        'deadline'     => $tutupSemester,
                        'sudah_input'  => $sudahInput,
                        'total_materi' => $totalMateri,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Error status input capaian: ' . $e->getMessage());
            }

            // ─── Data array untuk view ────────────────────────────────
            $data = [
                'nama_santri'             => $santri->nama_lengkap,
                'kelas'                   => $namaKelas,
                'progres_quran'           => round($progresAlquran, 1),
                'progres_hadist'          => round($progresHadist, 1),
                'progres_materi_tambahan' => round($progresMateriTambahan, 1),
                'saldo_uang_saku'         => $santri->saldo_uang_saku ?? 0,
                'poin_pelanggaran'        => RiwayatPelanggaran::where('id_santri', $idSantri)->sum('poin') ?? 0,
            ];

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
                'semesterAktif',
                'statistikKepulangan',
                'statistikKesehatan',
                'pelanggaranTerbaru',
                // ─── variabel baru ───
                'absensiPerKategori',
                'absensiPerKategoriMinggu',
                'statusInputCapaian'
            ));

        } catch (\Exception $e) {
            Log::error('=== FATAL ERROR DI DASHBOARD SANTRI ===');
            Log::error('Message: ' . $e->getMessage());
            Log::error('File: '    . $e->getFile());
            Log::error('Line: '    . $e->getLine());
            Log::error('Trace: '   . $e->getTraceAsString());

            if (config('app.debug')) {
                abort(500, 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            }
            abort(500, 'Terjadi kesalahan saat memuat dashboard. Silakan hubungi administrator.');
        }
    }
}