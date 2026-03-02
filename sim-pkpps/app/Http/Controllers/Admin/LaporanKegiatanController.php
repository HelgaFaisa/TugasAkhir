<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\KategoriKegiatan;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\KelompokKelas;
use App\Models\SantriKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKegiatanController extends Controller
{
    /**
     * ═══════════════════════════════════════════
     * A. INDEX - Main Dashboard Laporan Multi-Tab
     * ═══════════════════════════════════════════
     */
    public function index(Request $request)
    {
        // 1. Period Selector
        $periode = $request->get('periode', 'minggu_ini');
        [$startDate, $endDate] = $this->getPeriodeRange($periode, $request);
        [$prevStart, $prevEnd] = $this->getPreviousPeriodeRange($periode, $startDate, $endDate);

        $periodeLabel = $this->getPeriodeLabel($periode, $startDate, $endDate);

        // 2. KPI Cards
        $kpi = $this->calculateKpi($startDate, $endDate);
        $kpiPrev = $this->calculateKpi($prevStart, $prevEnd);

        $kpiComparison = [
            'total_kegiatan' => $kpi['total_kegiatan'] - $kpiPrev['total_kegiatan'],
            'avg_kehadiran' => round($kpi['avg_kehadiran'] - $kpiPrev['avg_kehadiran'], 1),
            'santri_perlu_perhatian' => $kpi['santri_perlu_perhatian'] - $kpiPrev['santri_perlu_perhatian'],
        ];

        // 3. Trend Data (line chart)
        $trendData = $this->getTrendData($startDate, $endDate);

        // 4. Distribusi Santri (funnel chart)
        $distribusiSantri = $this->getDistribusiSantri($startDate, $endDate);

        // 5. Top & Bottom Kegiatan
        $topKegiatan = $this->getTopBottomKegiatan($startDate, $endDate, 'top', 5);
        $bottomKegiatan = $this->getTopBottomKegiatan($startDate, $endDate, 'bottom', 5);

        // 6. Kehadiran Per Kelas
        $kehadiranPerKelas = $this->getKehadiranPerKelas($startDate, $endDate);

        // 7. Heatmap Kelas vs Kategori
        $heatmapData = Cache::remember(
            'laporan_heatmap_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd'),
            1800,
            fn() => $this->getHeatmapData($startDate, $endDate)
        );

        // 8. Pattern & Anomaly Detection
        $patterns = Cache::remember(
            'laporan_patterns_' . now()->format('Ymd'),
            3600,
            fn() => $this->patternDetection(new Request)
        );

        // 9. Santri Perlu Perhatian list (for Tab 3)
        $santriPerluPerhatianList = $this->getSantriPerluPerhatianList($startDate, $endDate, 10);

        // 10. Leaderboard (for Tab 3)
        $leaderboard = $this->getLeaderboard($startDate, $endDate, 10);

        // 11. Kegiatan Performance (for Tab 4)
        $kegiatanPerformance = $this->getKegiatanPerformance($startDate, $endDate);

        // Data filter
        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $kelasList = Kelas::active()->ordered()->with('kelompok')->get();

        return view('admin.kegiatan.laporan.index', compact(
            'periode', 'startDate', 'endDate', 'periodeLabel',
            'kpi', 'kpiPrev', 'kpiComparison',
            'trendData', 'distribusiSantri',
            'topKegiatan', 'bottomKegiatan',
            'kehadiranPerKelas', 'heatmapData',
            'patterns',
            'santriPerluPerhatianList', 'leaderboard',
            'kegiatanPerformance',
            'kategoris', 'kelasList'
        ));
    }

    /**
     * ═══════════════════════════════════════════
     * B. ANALISIS PER KELAS (AJAX)
     * ═══════════════════════════════════════════
     */
    public function analisPerKelas(Request $request)
    {
        $idKelas = $request->get('id_kelas');
        $kelas = Kelas::with('kelompok')->findOrFail($idKelas);

        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'bulan_ini'), $request
        );

        $santriIds = SantriKelas::where('id_kelas', $idKelas)->pluck('id_santri');

        // Kehadiran per santri
        $kehadiranPerSantri = [];
        if ($santriIds->isNotEmpty()) {
            $kehadiranPerSantri = AbsensiKegiatan::whereIn('id_santri', $santriIds)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->join('santris', 'absensi_kegiatans.id_santri', '=', 'santris.id_santri')
                ->select(
                    'santris.id_santri', 'santris.nama_lengkap',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Izin" THEN 1 ELSE 0 END) as izin'),
                    DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Sakit" THEN 1 ELSE 0 END) as sakit'),
                    DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Alpa" THEN 1 ELSE 0 END) as alpa')
                )
                ->groupBy('santris.id_santri', 'santris.nama_lengkap')
                ->orderBy('hadir', 'desc')
                ->get()
                ->map(function ($s) {
                    $s->persen = $s->total > 0 ? round(($s->hadir / $s->total) * 100, 1) : 0;
                    return $s;
                });
        }

        // Distribusi status
        $distribusi = AbsensiKegiatan::whereIn('id_santri', $santriIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Trend 4 minggu
        $trend = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = Carbon::parse($endDate)->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::parse($endDate)->subWeeks($i)->endOfWeek();

            $weekData = AbsensiKegiatan::whereIn('id_santri', $santriIds)
                ->whereBetween('tanggal', [$weekStart, $weekEnd])
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir')
                ->first();

            $trend[] = [
                'label' => 'Minggu ' . (4 - $i),
                'persen' => ($weekData->total ?? 0) > 0
                    ? round(($weekData->hadir / $weekData->total) * 100, 1) : 0,
            ];
        }

        return response()->json([
            'kelas' => $kelas,
            'kehadiran_per_santri' => $kehadiranPerSantri,
            'distribusi' => $distribusi,
            'trend' => $trend,
            'jumlah_santri' => $santriIds->count(),
        ]);
    }

    /**
     * ═══════════════════════════════════════════
     * C. DETAIL SANTRI - Individual Report
     * ═══════════════════════════════════════════
     */
    public function detailSantri($id_santri, Request $request)
    {
        $santri = Santri::where('id_santri', $id_santri)
            ->with('kelasSantri.kelas.kelompok')
            ->firstOrFail();

        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'bulan_ini'), $request
        );

        $periodeLabel = $this->getPeriodeLabel($request->get('periode', 'bulan_ini'), $startDate, $endDate);

        // Stats summary
        $stats = AbsensiKegiatan::where('id_santri', $id_santri)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status = "Izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN status = "Sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN status = "Alpa" THEN 1 ELSE 0 END) as alpa')
            )
            ->first() ?? (object) ['total' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

        $persenKehadiran = ($stats->total ?? 0) > 0 ? round(($stats->hadir / $stats->total) * 100, 1) : 0;

        // Kehadiran per kegiatan
        $perKegiatan = AbsensiKegiatan::where('id_santri', $id_santri)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->select(
                'kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Alpa" THEN 1 ELSE 0 END) as alpa')
            )
            ->groupBy('kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan')
            ->get()
            ->map(function ($k) {
                $k->persen = $k->total > 0 ? round(($k->hadir / $k->total) * 100, 1) : 0;
                return $k;
            });

        // Trend 4 minggu
        $trend = [];
        for ($i = 3; $i >= 0; $i--) {
            $ws = Carbon::parse($endDate)->subWeeks($i)->startOfWeek();
            $we = Carbon::parse($endDate)->subWeeks($i)->endOfWeek();

            $wd = AbsensiKegiatan::where('id_santri', $id_santri)
                ->whereBetween('tanggal', [$ws, $we])
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                ->first();

            $trend[] = [
                'label' => 'Mg ' . (4 - $i),
                'persen' => ($wd->total ?? 0) > 0 ? round(($wd->hadir / $wd->total) * 100, 1) : 0,
            ];
        }

        // Kegiatan paling sering bolos
        $kegiatanBolos = AbsensiKegiatan::where('id_santri', $id_santri)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('status', 'Alpa')
            ->select('kegiatan_id', DB::raw('COUNT(*) as total_alpa'))
            ->groupBy('kegiatan_id')
            ->orderByDesc('total_alpa')
            ->with('kegiatan:kegiatan_id,nama_kegiatan')
            ->first();

        // Streak: consecutive days hadir
        $streak = $this->calculateStreak($id_santri);

        // Riwayat absensi terbaru
        $riwayatTerbaru = AbsensiKegiatan::where('id_santri', $id_santri)
            ->with('kegiatan.kategori')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_absen')
            ->limit(30)
            ->get();

        return view('admin.kegiatan.laporan.detail-santri', compact(
            'santri', 'stats', 'persenKehadiran', 'perKegiatan',
            'trend', 'kegiatanBolos', 'streak',
            'riwayatTerbaru', 'startDate', 'endDate', 'periodeLabel'
        ));
    }

    /**
     * ═══════════════════════════════════════════
     * D. SANTRI PERLU PERHATIAN
     * ═══════════════════════════════════════════
     */
    public function santriPerluPerhatian(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'bulan_ini'), $request
        );

        $periodeLabel = $this->getPeriodeLabel($request->get('periode', 'bulan_ini'), $startDate, $endDate);

        $query = AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->join('santris', 'absensi_kegiatans.id_santri', '=', 'santris.id_santri')
            ->where('santris.status', 'Aktif')
            ->select(
                'santris.id_santri', 'santris.nama_lengkap',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Alpa" THEN 1 ELSE 0 END) as alpa'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) / COUNT(*) * 100, 1) as persen')
            )
            ->groupBy('santris.id_santri', 'santris.nama_lengkap')
            ->having('persen', '<', 70)
            ->orderBy('persen', 'asc');

        // Filter kelas
        if ($request->filled('id_kelas')) {
            $santriIdsInKelas = SantriKelas::where('id_kelas', $request->id_kelas)->pluck('id_santri');
            $query->whereIn('santris.id_santri', $santriIdsInKelas);
        }

        $santris = $query->paginate(20)->appends(request()->query());

        $kelasList = Kelas::active()->ordered()->with('kelompok')->get();

        return view('admin.kegiatan.laporan.santri-perlu-perhatian', compact(
            'santris', 'kelasList', 'startDate', 'endDate', 'periodeLabel'
        ));
    }

    /**
     * ═══════════════════════════════════════════
     * E. LEADERBOARD
     * ═══════════════════════════════════════════
     */
    public function leaderboard(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'bulan_ini'), $request
        );

        $limit = $request->get('limit', 10);
        $data = $this->getLeaderboard($startDate, $endDate, $limit, $request->get('id_kelas'));

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('admin.kegiatan.laporan.leaderboard', [
            'leaderboard' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * ═══════════════════════════════════════════
     * F. ANALISIS KEGIATAN (Deep Dive)
     * ═══════════════════════════════════════════
     */
    public function analisKegiatan($kegiatan_id, Request $request)
    {
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok'])
            ->where('kegiatan_id', $kegiatan_id)
            ->firstOrFail();

        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'bulan_ini'), $request
        );
        $periodeLabel = $this->getPeriodeLabel($request->get('periode', 'bulan_ini'), $startDate, $endDate);

        // Stats overview
        $stats = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status="Izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN status="Sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN status="Alpa" THEN 1 ELSE 0 END) as alpa')
            )
            ->first() ?? (object) ['total' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        $stats->persen = ($stats->total ?? 0) > 0 ? round(($stats->hadir / $stats->total) * 100, 1) : 0;

        // Trend 4 minggu
        $trend = [];
        for ($i = 3; $i >= 0; $i--) {
            $ws = Carbon::parse($endDate)->subWeeks($i)->startOfWeek();
            $we = Carbon::parse($endDate)->subWeeks($i)->endOfWeek();

            $wd = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
                ->whereBetween('tanggal', [$ws, $we])
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                ->first();

            $trend[] = [
                'label' => 'Mg ' . (4 - $i),
                'persen' => ($wd->total ?? 0) > 0 ? round(($wd->hadir / $wd->total) * 100, 1) : 0,
            ];
        }

        // Breakdown per kelas (if multiple)
        $breakdownPerKelas = [];
        if (!$kegiatan->isForAllClasses()) {
            foreach ($kegiatan->kelasKegiatan as $kelas) {
                $sIds = SantriKelas::where('id_kelas', $kelas->id)->pluck('id_santri');
                if ($sIds->isEmpty()) continue;

                $kd = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
                    ->whereIn('id_santri', $sIds)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                    ->first();

                $breakdownPerKelas[] = [
                    'kelas' => $kelas->nama_kelas,
                    'total' => $kd->total ?? 0,
                    'hadir' => $kd->hadir ?? 0,
                    'persen' => ($kd->total ?? 0) > 0 ? round(($kd->hadir / $kd->total) * 100, 1) : 0,
                ];
            }
        }

        // Punctuality (RFID data)
        $punctuality = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('status', 'Hadir')
            ->whereNotNull('waktu_absen')
            ->select(
                DB::raw('SUM(CASE WHEN TIME(waktu_absen) <= TIME(
                    (SELECT waktu_mulai FROM kegiatans WHERE kegiatan_id = absensi_kegiatans.kegiatan_id)
                ) THEN 1 ELSE 0 END) as tepat_waktu'),
                DB::raw('SUM(CASE WHEN TIME(waktu_absen) > TIME(
                    (SELECT waktu_mulai FROM kegiatans WHERE kegiatan_id = absensi_kegiatans.kegiatan_id)
                ) THEN 1 ELSE 0 END) as terlambat'),
                DB::raw('COUNT(*) as total')
            )
            ->first();

        // Santri tidak pernah hadir
        $santriTidakPernahHadir = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('id_santri', DB::raw('SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir'))
            ->groupBy('id_santri')
            ->having('hadir', '=', 0)
            ->with('santri:id_santri,nama_lengkap')
            ->get();

        // Insights
        $insights = $this->generateKegiatanInsights($kegiatan, $stats, $trend, $breakdownPerKelas);

        return view('admin.kegiatan.laporan.analisis-kegiatan', compact(
            'kegiatan', 'stats', 'trend', 'breakdownPerKelas',
            'punctuality', 'santriTidakPernahHadir', 'insights',
            'startDate', 'endDate', 'periodeLabel'
        ));
    }

    /**
     * ═══════════════════════════════════════════
     * G. PATTERN DETECTION
     * ═══════════════════════════════════════════
     */
    public function patternDetection(Request $request)
    {
        $patterns = [];

        // Pattern 1: Consistent Low Attendance (kegiatan <75% for >3 weeks)
        $threeWeeksAgo = Carbon::now()->subWeeks(3);
        $lowAttendance = AbsensiKegiatan::where('tanggal', '>=', $threeWeeksAgo)
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->select(
                'kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan',
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan')
            ->having('persen', '<', 75)
            ->get();

        foreach ($lowAttendance as $la) {
            $patterns[] = [
                'type' => 'warning',
                'category' => 'Kehadiran Rendah Konsisten',
                'title' => $la->nama_kegiatan . ' (' . $la->persen . '%)',
                'description' => "Kegiatan {$la->nama_kegiatan} memiliki kehadiran konsisten di bawah 75% selama 3 minggu terakhir.",
                'action_url' => route('admin.laporan-kegiatan.analisis-kegiatan', $la->kegiatan_id),
                'action_text' => 'Analisis Detail',
            ];
        }

        // Pattern 2: Day-Specific Low Attendance
        $dayStats = AbsensiKegiatan::where('tanggal', '>=', Carbon::now()->subMonth())
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->select(
                'kegiatans.hari',
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('kegiatans.hari')
            ->having('persen', '<', 70)
            ->get();

        foreach ($dayStats as $ds) {
            $patterns[] = [
                'type' => 'info',
                'category' => 'Pola Hari Tertentu',
                'title' => "Kehadiran rendah di hari {$ds->hari} ({$ds->persen}%)",
                'description' => "Rata-rata kehadiran di hari {$ds->hari} hanya {$ds->persen}% dalam sebulan terakhir.",
                'action_url' => null,
                'action_text' => null,
            ];
        }

        // Pattern 3: Class Attendance Drop (>10% drop vs last week)
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisWeekEnd = Carbon::now()->endOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $kelasAll = Kelas::active()->get();
        foreach ($kelasAll as $kelas) {
            $sIds = SantriKelas::where('id_kelas', $kelas->id)->pluck('id_santri');
            if ($sIds->isEmpty()) continue;

            $thisWeek = AbsensiKegiatan::whereIn('id_santri', $sIds)
                ->whereBetween('tanggal', [$thisWeekStart, $thisWeekEnd])
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                ->first();
            $lastWeek = AbsensiKegiatan::whereIn('id_santri', $sIds)
                ->whereBetween('tanggal', [$lastWeekStart, $lastWeekEnd])
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                ->first();

            $persenThis = ($thisWeek->total ?? 0) > 0 ? round(($thisWeek->hadir / $thisWeek->total) * 100, 1) : null;
            $persenLast = ($lastWeek->total ?? 0) > 0 ? round(($lastWeek->hadir / $lastWeek->total) * 100, 1) : null;

            if ($persenThis !== null && $persenLast !== null && ($persenLast - $persenThis) > 10) {
                $drop = round($persenLast - $persenThis, 1);
                $patterns[] = [
                    'type' => 'danger',
                    'category' => 'Penurunan Kelas',
                    'title' => "Kelas {$kelas->nama_kelas} turun {$drop}%",
                    'description' => "Kehadiran kelas {$kelas->nama_kelas} turun dari {$persenLast}% ke {$persenThis}% dalam seminggu.",
                    'action_url' => null,
                    'action_text' => 'Lihat Detail Kelas',
                ];
            }
        }

        // Pattern 4: Santri Absent Streak (3+ consecutive absences)
        $santriAbsentStreak = DB::select("
            SELECT s.id_santri, s.nama_lengkap, COUNT(*) as consecutive_absent
            FROM absensi_kegiatans a
            JOIN santris s ON a.id_santri = s.id_santri
            WHERE a.status = 'Alpa'
            AND a.tanggal >= ?
            AND s.status = 'Aktif'
            GROUP BY s.id_santri, s.nama_lengkap
            HAVING consecutive_absent >= 3
            ORDER BY consecutive_absent DESC
            LIMIT 10
        ", [Carbon::now()->subWeeks(2)->format('Y-m-d')]);

        foreach ($santriAbsentStreak as $sas) {
            $patterns[] = [
                'type' => 'danger',
                'category' => 'Absen Beruntun',
                'title' => "{$sas->nama_lengkap} ({$sas->consecutive_absent}x Alpa)",
                'description' => "Santri {$sas->nama_lengkap} tercatat {$sas->consecutive_absent} kali Alpa dalam 2 minggu terakhir.",
                'action_url' => route('admin.laporan-kegiatan.detail-santri', $sas->id_santri),
                'action_text' => 'Lihat Detail',
            ];
        }

        if ($request->ajax()) {
            return response()->json($patterns);
        }

        return $patterns;
    }

    /**
     * ═══════════════════════════════════════════
     * H. EXPORT EXCEL (CSV Fallback)
     * ═══════════════════════════════════════════
     */
    public function exportExcel(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'bulan_ini'), $request
        );

        $contents = $request->get('content', ['summary']);

        $filename = 'laporan_kegiatan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($startDate, $endDate, $contents) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Sheet 1: Summary
            if (in_array('summary', $contents)) {
                fputcsv($handle, ['=== RINGKASAN LAPORAN KEGIATAN ===']);
                fputcsv($handle, ['Periode', $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y')]);

                $kpi = $this->calculateKpi($startDate, $endDate);
                fputcsv($handle, ['Total Kegiatan', $kpi['total_kegiatan']]);
                fputcsv($handle, ['Rata-rata Kehadiran', $kpi['avg_kehadiran'] . '%']);
                fputcsv($handle, ['Kegiatan Terbaik', $kpi['kegiatan_terbaik']['nama'] ?? '-', ($kpi['kegiatan_terbaik']['persen'] ?? 0) . '%']);
                fputcsv($handle, ['Santri Perlu Perhatian', $kpi['santri_perlu_perhatian']]);
                fputcsv($handle, []);
            }

            // Per Kelas
            if (in_array('per_kelas', $contents)) {
                fputcsv($handle, ['=== KEHADIRAN PER KELAS ===']);
                fputcsv($handle, ['Kelompok', 'Kelas', 'Jumlah Santri', 'Total Absensi', 'Hadir', 'Izin', 'Sakit', 'Alpa', '% Kehadiran']);

                $perKelas = $this->getKehadiranPerKelas($startDate, $endDate);
                foreach ($perKelas as $kelompok) {
                    foreach ($kelompok['kelas'] as $k) {
                        fputcsv($handle, [
                            $kelompok['nama_kelompok'], $k['nama_kelas'], $k['jumlah_santri'],
                            $k['total'], $k['hadir'], $k['izin'], $k['sakit'], $k['alpa'], $k['persen'] . '%'
                        ]);
                    }
                }
                fputcsv($handle, []);
            }

            // Per Santri (warning: large)
            if (in_array('per_santri', $contents)) {
                fputcsv($handle, ['=== DETAIL PER SANTRI ===']);
                fputcsv($handle, ['ID Santri', 'Nama', 'Total', 'Hadir', 'Izin', 'Sakit', 'Alpa', '% Kehadiran']);

                $perSantri = AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
                    ->join('santris', 'absensi_kegiatans.id_santri', '=', 'santris.id_santri')
                    ->where('santris.status', 'Aktif')
                    ->select(
                        'santris.id_santri', 'santris.nama_lengkap',
                        DB::raw('COUNT(*) as total'),
                        DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END) as hadir'),
                        DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Izin" THEN 1 ELSE 0 END) as izin'),
                        DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Sakit" THEN 1 ELSE 0 END) as sakit'),
                        DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Alpa" THEN 1 ELSE 0 END) as alpa')
                    )
                    ->groupBy('santris.id_santri', 'santris.nama_lengkap')
                    ->orderBy('santris.nama_lengkap')
                    ->get();

                foreach ($perSantri as $s) {
                    $persen = $s->total > 0 ? round(($s->hadir / $s->total) * 100, 1) : 0;
                    fputcsv($handle, [$s->id_santri, $s->nama_lengkap, $s->total, $s->hadir, $s->izin, $s->sakit, $s->alpa, $persen . '%']);
                }
                fputcsv($handle, []);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * ═══════════════════════════════════════════
     * I. EXPORT PDF
     * ═══════════════════════════════════════════
     */
    public function exportPdf(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'bulan_ini'), $request
        );

        $periodeLabel = $this->getPeriodeLabel($request->get('periode', 'bulan_ini'), $startDate, $endDate);

        $kpi = $this->calculateKpi($startDate, $endDate);
        $kehadiranPerKelas = $this->getKehadiranPerKelas($startDate, $endDate);
        $topKegiatan = $this->getTopBottomKegiatan($startDate, $endDate, 'top', 5);
        $bottomKegiatan = $this->getTopBottomKegiatan($startDate, $endDate, 'bottom', 5);
        $distribusiSantri = $this->getDistribusiSantri($startDate, $endDate);
        $santriPerluPerhatianList = $this->getSantriPerluPerhatianList($startDate, $endDate, 15);

        $pdf = Pdf::loadView('admin.kegiatan.laporan.pdf-template', compact(
            'kpi', 'periodeLabel', 'startDate', 'endDate',
            'kehadiranPerKelas', 'topKegiatan', 'bottomKegiatan',
            'distribusiSantri', 'santriPerluPerhatianList'
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('laporan_kegiatan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf');
    }

    /**
     * ═══════════════════════════════════════════
     * J. REFRESH KPI (AJAX)
     * ═══════════════════════════════════════════
     */
    public function refreshKpi(Request $request)
    {
        [$startDate, $endDate] = $this->getPeriodeRange(
            $request->get('periode', 'minggu_ini'), $request
        );

        $kpi = $this->calculateKpi($startDate, $endDate);

        return response()->json($kpi);
    }

    // ═══════════════════════════════════════════
    // PRIVATE HELPER METHODS
    // ═══════════════════════════════════════════

    /**
     * Get date range for a given period
     */
    private function getPeriodeRange($periode, Request $request = null)
    {
        switch ($periode) {
            case 'hari_ini':
                return [Carbon::today(), Carbon::today()];
            case 'minggu_ini':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'bulan_ini':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'semester_ini':
                $now = Carbon::now();
                if ($now->month >= 7) {
                    return [Carbon::create($now->year, 7, 1), Carbon::create($now->year, 12, 31)];
                }
                return [Carbon::create($now->year, 1, 1), Carbon::create($now->year, 6, 30)];
            case 'custom':
                $dari = $request ? $request->get('tanggal_dari', Carbon::now()->startOfMonth()->format('Y-m-d')) : Carbon::now()->startOfMonth()->format('Y-m-d');
                $sampai = $request ? $request->get('tanggal_sampai', Carbon::now()->format('Y-m-d')) : Carbon::now()->format('Y-m-d');
                return [Carbon::parse($dari), Carbon::parse($sampai)];
            default:
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        }
    }

    /**
     * Get the previous period range (same length, shifted back)
     */
    private function getPreviousPeriodeRange($periode, $startDate, $endDate)
    {
        $diff = $startDate->diffInDays($endDate) + 1;
        return [
            Carbon::parse($startDate)->subDays($diff),
            Carbon::parse($endDate)->subDays($diff),
        ];
    }

    /**
     * Human-readable period label
     */
    private function getPeriodeLabel($periode, $startDate, $endDate)
    {
        $labels = [
            'hari_ini' => 'Hari Ini (' . Carbon::today()->locale('id')->isoFormat('D MMMM YYYY') . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => Carbon::now()->locale('id')->isoFormat('MMMM YYYY'),
            'semester_ini' => 'Semester ' . (Carbon::now()->month >= 7 ? 'Ganjil' : 'Genap') . ' ' . Carbon::now()->year,
            'custom' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
        ];
        return $labels[$periode] ?? $labels['minggu_ini'];
    }

    /**
     * Calculate KPI metrics
     */
    private function calculateKpi($startDate, $endDate)
    {
        // Total kegiatan unik
        $totalKegiatan = AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->distinct('kegiatan_id')
            ->count('kegiatan_id');

        // Rata-rata kehadiran
        $avgData = AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
            ->first();
        $avgKehadiran = ($avgData->total ?? 0) > 0 ? round(($avgData->hadir / $avgData->total) * 100, 1) : 0;

        // Kegiatan terbaik
        $kegiatanTerbaik = AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->select(
                'kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan',
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan')
            ->orderByDesc('persen')
            ->first();

        // Santri perlu perhatian (<70%)
        $santriPerluPerhatian = DB::table('absensi_kegiatans')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->join('santris', 'absensi_kegiatans.id_santri', '=', 'santris.id_santri')
            ->where('santris.status', 'Aktif')
            ->select(
                'santris.id_santri',
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('santris.id_santri')
            ->having('persen', '<', 70)
            ->get()
            ->count();

        return [
            'total_kegiatan' => $totalKegiatan,
            'avg_kehadiran' => $avgKehadiran,
            'kegiatan_terbaik' => $kegiatanTerbaik ? [
                'nama' => $kegiatanTerbaik->nama_kegiatan,
                'persen' => $kegiatanTerbaik->persen,
            ] : ['nama' => '-', 'persen' => 0],
            'santri_perlu_perhatian' => $santriPerluPerhatian,
        ];
    }

    /**
     * Get trend data for line chart
     */
    private function getTrendData($startDate, $endDate)
    {
        $diffDays = $startDate->diffInDays($endDate);
        $groupBy = $diffDays > 14 ? 'week' : 'day';

        $kategoris = KategoriKegiatan::all();
        $labels = [];
        $datasets = [];

        if ($groupBy === 'week') {
            $current = Carbon::parse($startDate)->startOfWeek();
            $weekNum = 1;
            while ($current->lte($endDate)) {
                $labels[] = 'Mg ' . $weekNum;
                $weekNum++;
                $current->addWeek();
            }

            foreach ($kategoris as $kategori) {
                $data = [];
                $current = Carbon::parse($startDate)->startOfWeek();
                while ($current->lte($endDate)) {
                    $ws = Carbon::parse($current);
                    $we = Carbon::parse($current)->endOfWeek();

                    $wd = AbsensiKegiatan::whereBetween('tanggal', [$ws, $we])
                        ->whereHas('kegiatan', fn($q) => $q->where('kategori_id', $kategori->kategori_id))
                        ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                        ->first();

                    $data[] = ($wd->total ?? 0) > 0 ? round(($wd->hadir / $wd->total) * 100, 1) : null;
                    $current->addWeek();
                }
                $datasets[] = ['kategori' => $kategori->nama_kategori, 'data' => $data];
            }
        } else {
            $current = Carbon::parse($startDate);
            while ($current->lte($endDate)) {
                $labels[] = $current->format('d/m');

                $current->addDay();
            }

            foreach ($kategoris as $kategori) {
                $data = [];
                $current = Carbon::parse($startDate);
                while ($current->lte($endDate)) {
                    $wd = AbsensiKegiatan::whereDate('tanggal', $current)
                        ->whereHas('kegiatan', fn($q) => $q->where('kategori_id', $kategori->kategori_id))
                        ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                        ->first();

                    $data[] = ($wd->total ?? 0) > 0 ? round(($wd->hadir / $wd->total) * 100, 1) : null;
                    $current->addDay();
                }
                $datasets[] = ['kategori' => $kategori->nama_kategori, 'data' => $data];
            }
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }

    /**
     * Get santri distribution by attendance percentage
     */
    private function getDistribusiSantri($startDate, $endDate)
    {
        $santriStats = DB::table('absensi_kegiatans')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->join('santris', 'absensi_kegiatans.id_santri', '=', 'santris.id_santri')
            ->where('santris.status', 'Aktif')
            ->select(
                'santris.id_santri',
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('santris.id_santri')
            ->get();

        $distribusi = [
            'Perfect (100%)' => 0,
            'Sangat Baik (95-99%)' => 0,
            'Baik (85-94%)' => 0,
            'Cukup (75-84%)' => 0,
            'Perlu Perhatian (<75%)' => 0,
        ];

        foreach ($santriStats as $s) {
            if ($s->persen >= 100) $distribusi['Perfect (100%)']++;
            elseif ($s->persen >= 95) $distribusi['Sangat Baik (95-99%)']++;
            elseif ($s->persen >= 85) $distribusi['Baik (85-94%)']++;
            elseif ($s->persen >= 75) $distribusi['Cukup (75-84%)']++;
            else $distribusi['Perlu Perhatian (<75%)']++;
        }

        $total = $santriStats->count();

        return collect($distribusi)->map(function ($count, $label) use ($total) {
            return [
                'label' => $label,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        })->values()->toArray();
    }

    /**
     * Get top or bottom kegiatan by attendance
     */
    private function getTopBottomKegiatan($startDate, $endDate, $type = 'top', $limit = 5)
    {
        $order = $type === 'top' ? 'desc' : 'asc';

        return AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->leftJoin('kategori_kegiatans', 'kegiatans.kategori_id', '=', 'kategori_kegiatans.kategori_id')
            ->select(
                'kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan',
                'kategori_kegiatans.nama_kategori',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan', 'kategori_kegiatans.nama_kategori')
            ->orderBy('persen', $order)
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get kehadiran per kelas grouped by kelompok
     */
    private function getKehadiranPerKelas($startDate, $endDate)
    {
        $kelompoks = KelompokKelas::active()->ordered()
            ->with(['kelas' => fn($q) => $q->active()->ordered()])
            ->get();

        $result = [];
        foreach ($kelompoks as $kelompok) {
            $kelasData = [];
            foreach ($kelompok->kelas as $kelas) {
                $santriIds = SantriKelas::where('id_kelas', $kelas->id)->pluck('id_santri');
                if ($santriIds->isEmpty()) continue;

                $absensi = AbsensiKegiatan::whereIn('id_santri', $santriIds)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status="Izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN status="Sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN status="Alpa" THEN 1 ELSE 0 END) as alpa
                    ')
                    ->first();

                $kelasData[] = [
                    'id_kelas' => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'jumlah_santri' => $santriIds->count(),
                    'total' => $absensi->total ?? 0,
                    'hadir' => $absensi->hadir ?? 0,
                    'izin' => $absensi->izin ?? 0,
                    'sakit' => $absensi->sakit ?? 0,
                    'alpa' => $absensi->alpa ?? 0,
                    'persen' => ($absensi->total ?? 0) > 0
                        ? round(($absensi->hadir / $absensi->total) * 100, 1) : 0,
                ];
            }
            if (!empty($kelasData)) {
                $result[] = [
                    'nama_kelompok' => $kelompok->nama_kelompok,
                    'kelas' => $kelasData,
                ];
            }
        }
        return $result;
    }

    /**
     * Get heatmap data: Kelas vs Kategori
     */
    private function getHeatmapData($startDate, $endDate)
    {
        $kategoris = KategoriKegiatan::all();
        $kelasList = Kelas::active()->ordered()->with('kelompok')->get();

        $heatmap = [];
        foreach ($kelasList as $kelas) {
            $santriIds = SantriKelas::where('id_kelas', $kelas->id)->pluck('id_santri');
            $row = ['kelas' => $kelas->nama_kelas, 'kelompok' => $kelas->kelompok->nama_kelompok ?? '-', 'data' => []];

            foreach ($kategoris as $kategori) {
                if ($santriIds->isEmpty()) {
                    $row['data'][$kategori->nama_kategori] = null;
                    continue;
                }

                // Check if this kelas has kegiatan in this kategori
                $kegiatanIds = Kegiatan::where('kategori_id', $kategori->kategori_id)
                    ->pluck('kegiatan_id');

                $absensi = AbsensiKegiatan::whereIn('id_santri', $santriIds)
                    ->whereIn('kegiatan_id', $kegiatanIds)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as hadir')
                    ->first();

                $row['data'][$kategori->nama_kategori] = ($absensi->total ?? 0) > 0
                    ? round(($absensi->hadir / $absensi->total) * 100, 1) : null;
            }
            $heatmap[] = $row;
        }

        return ['rows' => $heatmap, 'columns' => $kategoris->pluck('nama_kategori')->toArray()];
    }

    /**
     * Get santri perlu perhatian list
     */
    private function getSantriPerluPerhatianList($startDate, $endDate, $limit = 10)
    {
        return AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->join('santris', 'absensi_kegiatans.id_santri', '=', 'santris.id_santri')
            ->where('santris.status', 'Aktif')
            ->select(
                'santris.id_santri', 'santris.nama_lengkap',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Alpa" THEN 1 ELSE 0 END) as alpa'),
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('santris.id_santri', 'santris.nama_lengkap')
            ->having('persen', '<', 70)
            ->orderBy('persen', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get leaderboard (top santri)
     */
    private function getLeaderboard($startDate, $endDate, $limit = 10, $idKelas = null)
    {
        $query = AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->join('santris', 'absensi_kegiatans.id_santri', '=', 'santris.id_santri')
            ->where('santris.status', 'Aktif')
            ->select(
                'santris.id_santri', 'santris.nama_lengkap',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('santris.id_santri', 'santris.nama_lengkap')
            ->orderByDesc('persen')
            ->orderByDesc('hadir')
            ->limit($limit);

        if ($idKelas) {
            $sIds = SantriKelas::where('id_kelas', $idKelas)->pluck('id_santri');
            $query->whereIn('santris.id_santri', $sIds);
        }

        return $query->get()->map(function ($s) {
            $s->streak = $this->calculateStreak($s->id_santri);
            return $s;
        });
    }

    /**
     * Get kegiatan performance table
     */
    private function getKegiatanPerformance($startDate, $endDate)
    {
        return AbsensiKegiatan::whereBetween('tanggal', [$startDate, $endDate])
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->leftJoin('kategori_kegiatans', 'kegiatans.kategori_id', '=', 'kategori_kegiatans.kategori_id')
            ->select(
                'kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan', 'kegiatans.hari',
                'kategori_kegiatans.nama_kategori',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('ROUND(SUM(CASE WHEN absensi_kegiatans.status="Hadir" THEN 1 ELSE 0 END)/COUNT(*)*100,1) as persen')
            )
            ->groupBy('kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan', 'kegiatans.hari', 'kategori_kegiatans.nama_kategori')
            ->orderByDesc('persen')
            ->get();
    }

    /**
     * Calculate consecutive hadir streak for a santri
     */
    private function calculateStreak($id_santri)
    {
        $absensis = AbsensiKegiatan::where('id_santri', $id_santri)
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_absen')
            ->select('status')
            ->limit(50)
            ->get();

        $streak = 0;
        foreach ($absensis as $a) {
            if ($a->status === 'Hadir') {
                $streak++;
            } else {
                break;
            }
        }
        return $streak;
    }

    /**
     * Generate insights for a specific kegiatan
     */
    private function generateKegiatanInsights($kegiatan, $stats, $trend, $breakdownPerKelas)
    {
        $insights = [];

        // Insight: Overall performance
        if ($stats->persen >= 90) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'fas fa-star',
                'text' => "Kegiatan {$kegiatan->nama_kegiatan} memiliki kehadiran sangat baik ({$stats->persen}%).",
            ];
        } elseif ($stats->persen < 70) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'text' => "Kehadiran {$kegiatan->nama_kegiatan} di bawah standar ({$stats->persen}%). Perlu evaluasi.",
            ];
        }

        // Insight: Trend direction
        if (count($trend) >= 2) {
            $last = end($trend)['persen'];
            $prev = $trend[count($trend) - 2]['persen'];
            $diff = $last - $prev;
            if ($diff > 5) {
                $insights[] = [
                    'type' => 'success',
                    'icon' => 'fas fa-arrow-up',
                    'text' => "Tren kehadiran meningkat +{$diff}% dalam minggu terakhir.",
                ];
            } elseif ($diff < -5) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => 'fas fa-arrow-down',
                    'text' => "Tren kehadiran menurun {$diff}% dalam minggu terakhir.",
                ];
            }
        }

        // Insight: Kelas with lowest attendance
        if (!empty($breakdownPerKelas)) {
            $lowest = collect($breakdownPerKelas)->sortBy('persen')->first();
            if ($lowest && $lowest['persen'] < 70) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => 'fas fa-users',
                    'text' => "Kelas {$lowest['kelas']} memiliki kehadiran terendah ({$lowest['persen']}%).",
                ];
            }
        }

        // Insight: Alpa count
        if ($stats->alpa > 0) {
            $alpaPercent = $stats->total > 0 ? round(($stats->alpa / $stats->total) * 100, 1) : 0;
            if ($alpaPercent > 10) {
                $insights[] = [
                    'type' => 'danger',
                    'icon' => 'fas fa-user-times',
                    'text' => "Tingkat Alpa mencapai {$alpaPercent}% ({$stats->alpa} kali). Perlu tindakan segera.",
                ];
            }
        }

        return $insights;
    }
}
