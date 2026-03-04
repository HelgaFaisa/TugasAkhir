<?php
// app/Http/Controllers/Santri/RiwayatKegiatanSantriController.php
namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatKegiatanSantriController extends Controller
{
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    private function resolveDateRange(Request $request, string $defaultPreset = 'today'): array
    {
        $preset = $request->input('preset', $defaultPreset);
        $now    = Carbon::now();

        switch ($preset) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'today'];
            case 'this_week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'this_week'];
            case 'last_30':
                return [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay(), 'last_30'];
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), 'this_month'];
            case 'last_month':
                $lm = $now->copy()->subMonth();
                return [$lm->copy()->startOfMonth(), $lm->copy()->endOfMonth(), 'last_month'];
            default:
                $from = $request->filled('date_from')
                    ? Carbon::parse($request->date_from)->startOfDay()
                    : $now->copy()->startOfDay();
                $to = $request->filled('date_to')
                    ? Carbon::parse($request->date_to)->endOfDay()
                    : $now->copy()->endOfDay();
                if ($from->gt($to)) [$from, $to] = [$to, $from];
                return [$from, $to, 'custom'];
        }
    }

    // ================================================================
    //  INDEX
    // ================================================================
    public function index(Request $request)
    {
        $idSantri = $this->getSantriId();

        $santri = Santri::where('id_santri', $idSantri)
            ->with(['kelasPrimary.kelas'])
            ->select('id_santri', 'nama_lengkap', 'nis', 'status')
            ->firstOrFail();

        $namaKelas = optional(optional($santri->kelasPrimary)->kelas)->nama_kelas ?? '-';
        $activeTab = $request->input('tab', 'statistik');

        // ── Statistik range ───────────────────────────────────
        $statPresetReq = $request->input('preset_stat', $request->input('preset', 'this_week'));
        [$statFrom, $statTo, $statPreset] = $this->resolveDateRange(
            $request->merge([
                'preset'    => $statPresetReq,
                'date_from' => $request->input('stat_date_from'),
                'date_to'   => $request->input('stat_date_to'),
            ]),
            'this_week'
        );
        if ($statPreset === 'custom') {
            $statFrom = $request->filled('stat_date_from') ? Carbon::parse($request->stat_date_from)->startOfDay() : $statFrom;
            $statTo   = $request->filled('stat_date_to')   ? Carbon::parse($request->stat_date_to)->endOfDay()     : $statTo;
        }

        // ── Jadwal range ──────────────────────────────────────
        $jadPresetReq = $request->input('preset_jad', $request->input('preset', 'today'));
        [$jadFrom, $jadTo, $jadPreset] = $this->resolveDateRange(
            $request->merge([
                'preset'    => $jadPresetReq,
                'date_from' => $request->input('jad_date_from'),
                'date_to'   => $request->input('jad_date_to'),
            ]),
            'today'
        );

        // ── Mapping hari Carbon → nama hari di DB ─────────────
        $hariMapDb = [
            'Senin'  => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu'   => 'Rabu',
            'Kamis'  => 'Kamis',
            'Jumat'  => 'Jumat',
            'Sabtu'  => 'Sabtu',
            'Minggu' => 'Ahad',
        ];
        $hariCarbon = Carbon::now()->locale('id')->dayName;
        $hariIni    = $hariMapDb[$hariCarbon] ?? $hariCarbon;

        // ── KPI stats (stat range) ────────────────────────────
        $statFromStr = $statFrom->format('Y-m-d');
        $statToStr   = $statTo->format('Y-m-d');

        $statsRange = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereBetween('tanggal', [$statFromStr, $statToStr])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalRange     = array_sum($statsRange);
        $hadirRange     = $statsRange['Hadir']     ?? 0;
        $terlambatRange = $statsRange['Terlambat'] ?? 0;
        $izinRange      = $statsRange['Izin']      ?? 0;
        $sakitRange     = $statsRange['Sakit']     ?? 0;
        $alpaRange      = $statsRange['Alpa']      ?? 0;
        $pulangRange    = $statsRange['Pulang']    ?? 0;

        // ── Expected total: semua kegiatan di hari itu, tanpa filter kelas ──
        $expectedTotal = 0;
        $curStat = $statFrom->copy();
        while ($curStat->lte($statTo)) {
            $hariDb = $hariMapDb[$curStat->locale('id')->dayName] ?? $curStat->locale('id')->dayName;
            $expectedTotal += Kegiatan::where('hari', $hariDb)->count();
            $curStat->addDay();
        }

        $belumAbsenRange     = max(0, $expectedTotal - $totalRange);
        $hadirEfektif        = $hadirRange + $terlambatRange;
        $persentaseKehadiran = $expectedTotal > 0 ? round($hadirEfektif / $expectedTotal * 100, 1) : 0;

        // ── Jadwal dalam range: semua kegiatan, tanpa filter kelas ───
        $hariDalamRange = [];
        $cursor = $jadFrom->copy();
        while ($cursor->lte($jadTo)) {
            $hariDb = $hariMapDb[$cursor->locale('id')->dayName] ?? $cursor->locale('id')->dayName;
            $hariDalamRange[$hariDb] = true;
            $cursor->addDay();
        }
        $hariDalamRange = array_keys($hariDalamRange);

        $jadwalDalamRange = Kegiatan::with('kategori')
            ->whereIn('hari', $hariDalamRange)
            ->select('kegiatan_id', 'kategori_id', 'nama_kegiatan', 'waktu_mulai', 'waktu_selesai', 'hari', 'materi')
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad')")
            ->orderBy('waktu_mulai')
            ->get();

        // ── Status absensi santri dalam range jadwal ──────────
        $absensiDalamRange = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereBetween('tanggal', [$jadFrom->format('Y-m-d'), $jadTo->format('Y-m-d')])
            ->pluck('status', 'kegiatan_id')
            ->toArray();

        $absensiHariIni = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereDate('tanggal', Carbon::today())
            ->pluck('status', 'kegiatan_id')
            ->toArray();

        // ── Streak ───────────────────────────────────────────
        $streak = 0;
        AbsensiKegiatan::where('id_santri', $idSantri)
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_absen')
            ->select('status')
            ->limit(60)
            ->each(function ($a) use (&$streak) {
                if (in_array($a->status, ['Hadir', 'Terlambat'])) $streak++;
                else return false;
            });

        // ── Grafik tren ───────────────────────────────────────
        $diffDays   = $statFrom->diffInDays($statTo);
        $dataGrafik = [];

        if ($diffDays <= 31) {
            $cur = $statFrom->copy();
            while ($cur->lte($statTo)) {
                $d     = $cur->format('Y-m-d');
                $hadir = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->whereDate('tanggal', $d)
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();
                $total = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->whereDate('tanggal', $d)
                    ->count();
                $dataGrafik[] = ['label' => $cur->format('d/m'), 'hadir' => $hadir, 'total' => $total];
                $cur->addDay();
            }
        } else {
            $cur = $statFrom->copy()->startOfWeek();
            while ($cur->lte($statTo)) {
                $wStart = $cur->copy()->max($statFrom);
                $wEnd   = $cur->copy()->endOfWeek()->min($statTo);
                $hadir  = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->whereBetween('tanggal', [$wStart->format('Y-m-d'), $wEnd->format('Y-m-d')])
                    ->whereIn('status', ['Hadir', 'Terlambat'])
                    ->count();
                $total  = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->whereBetween('tanggal', [$wStart->format('Y-m-d'), $wEnd->format('Y-m-d')])
                    ->count();
                $dataGrafik[] = [
                    'label' => $wStart->format('d/m') . '–' . $wEnd->format('d/m'),
                    'hadir' => $hadir,
                    'total' => $total,
                ];
                $cur->addWeek();
            }
        }

        // ── Recent Absensi (8 terbaru dalam stat range) ───────
        $recentAbsensi = AbsensiKegiatan::with('kegiatan.kategori')
            ->where('id_santri', $idSantri)
            ->whereBetween('tanggal', [$statFromStr, $statToStr])
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->limit(8)
            ->get();

        // ── Heatmap kalender ──────────────────────────────────
        $heatmapMonths = [];
        $cur = $statFrom->copy()->startOfMonth();
        while ($cur->lte($statTo)) {
            $daysInMonth    = $cur->daysInMonth;
            $firstDayOfWeek = $cur->copy()->startOfMonth()->dayOfWeekIso;
            $days = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = $cur->format('Y-m') . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                $rows = AbsensiKegiatan::where('id_santri', $idSantri)->whereDate('tanggal', $date)->get();
                $level = 0;
                if ($rows->count() > 0) {
                    $hadirCount = $rows->whereIn('status', ['Hadir', 'Terlambat'])->count();
                    $pct        = round($hadirCount / $rows->count() * 100);
                    $level      = $pct >= 90 ? 4 : ($pct >= 70 ? 3 : ($pct >= 50 ? 2 : 1));
                }
                $days[] = [
                    'day'      => $d,
                    'date'     => $date,
                    'level'    => $level,
                    'count'    => $rows->whereIn('status', ['Hadir', 'Terlambat'])->count(),
                    'total'    => $rows->count(),
                    'is_today' => $date === Carbon::today()->format('Y-m-d'),
                    'in_range' => $date >= $statFromStr && $date <= $statToStr,
                ];
            }
            $heatmapMonths[] = [
                'label'          => $cur->locale('id')->isoFormat('MMMM YYYY'),
                'firstDayOfWeek' => $firstDayOfWeek,
                'days'           => $days,
            ];
            $cur->addMonth();
        }

        return view('santri.kegiatan.index', compact(
            'santri', 'namaKelas',
            'jadwalDalamRange', 'absensiDalamRange', 'absensiHariIni', 'hariIni',
            'jadPreset', 'jadFrom', 'jadTo',
            'statsRange', 'totalRange',
            'hadirRange', 'terlambatRange', 'izinRange', 'sakitRange', 'alpaRange', 'pulangRange',
            'hadirEfektif',
            'persentaseKehadiran', 'streak', 'expectedTotal', 'belumAbsenRange',
            'dataGrafik', 'statPreset', 'statFrom', 'statTo', 'statFromStr', 'statToStr', 'diffDays',
            'recentAbsensi',
            'heatmapMonths',
            'activeTab', 'hariIni'
        ));
    }

    // ================================================================
    //  SHOW — support filter tanggal, semua data ikut filter
    // ================================================================
    public function show($kegiatan_id, Request $request)
    {
        $idSantri = $this->getSantriId();

        $santri = Santri::where('id_santri', $idSantri)
            ->select('id_santri', 'nama_lengkap', 'nis', 'status')
            ->firstOrFail();

        $kegiatan = Kegiatan::with('kategori')
            ->where('kegiatan_id', $kegiatan_id)
            ->firstOrFail();

        // ── Resolve date range ────────────────────────────────
        $preset = $request->input('preset', 'this_week');
        $now    = Carbon::now();

        switch ($preset) {
            case 'this_week':
                $dateFrom = $now->copy()->startOfWeek();
                $dateTo   = $now->copy()->endOfWeek();
                break;
            case 'this_month':
                $dateFrom = $now->copy()->startOfMonth();
                $dateTo   = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $dateFrom = $now->copy()->subMonth()->startOfMonth();
                $dateTo   = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'last_3m':
                $dateFrom = $now->copy()->subMonths(3)->startOfDay();
                $dateTo   = $now->copy()->endOfDay();
                break;
            case 'all':
                $oldest   = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->where('kegiatan_id', $kegiatan_id)
                    ->min('tanggal');
                $dateFrom = $oldest
                    ? Carbon::parse($oldest)->startOfDay()
                    : $now->copy()->startOfWeek();
                $dateTo   = $now->copy()->endOfDay();
                break;
            default:
                $dateFrom = $request->filled('date_from')
                    ? Carbon::parse($request->date_from)->startOfDay()
                    : $now->copy()->startOfWeek();
                $dateTo   = $request->filled('date_to')
                    ? Carbon::parse($request->date_to)->endOfDay()
                    : $now->copy()->endOfWeek();
                if ($dateFrom->gt($dateTo)) [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
                $preset = 'custom';
        }

        $fromStr = $dateFrom->format('Y-m-d');
        $toStr   = $dateTo->format('Y-m-d');

        // ── Stats dalam range ─────────────────────────────────
        $stats = AbsensiKegiatan::where('id_santri', $idSantri)
            ->where('kegiatan_id', $kegiatan_id)
            ->whereBetween('tanggal', [$fromStr, $toStr])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalAbsensi    = array_sum($stats);
        $hadirEfektif    = ($stats['Hadir'] ?? 0) + ($stats['Terlambat'] ?? 0);
        $persentaseHadir = $totalAbsensi > 0
            ? round($hadirEfektif / $totalAbsensi * 100, 1) : 0;

        // ── Riwayat tabel (paginated, ikut range) ─────────────
        $riwayats = AbsensiKegiatan::where('id_santri', $idSantri)
            ->where('kegiatan_id', $kegiatan_id)
            ->whereBetween('tanggal', [$fromStr, $toStr])
            ->orderBy('tanggal', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // ── Lookup tanggal => status untuk kalender visual ────
        // Query terpisah agar tidak terbatas oleh pagination $riwayats
        $absensiByDate = AbsensiKegiatan::where('id_santri', $idSantri)
            ->where('kegiatan_id', $kegiatan_id)
            ->whereBetween('tanggal', [$fromStr, $toStr])
            ->select('tanggal', 'status')
            ->get()
            ->mapWithKeys(fn($a) => [Carbon::parse($a->tanggal)->format('Y-m-d') => $a->status])
            ->toArray();

        // ── Tren data ─────────────────────────────────────────
        $diffDays  = $dateFrom->diffInDays($dateTo);
        $trendData = [];

        if ($diffDays <= 31) {
            $cur = $dateFrom->copy();
            while ($cur->lte($dateTo)) {
                $d    = $cur->format('Y-m-d');
                $data = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->where('kegiatan_id', $kegiatan_id)
                    ->whereDate('tanggal', $d)
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();
                $trendData[] = [
                    'label' => $cur->format('d/m'),
                    'hadir' => ($data['Hadir'] ?? 0) + ($data['Terlambat'] ?? 0),
                    'total' => array_sum($data),
                ];
                $cur->addDay();
            }
            $trendLabel = 'Harian';
        } else {
            $cur = $dateFrom->copy()->startOfWeek();
            while ($cur->lte($dateTo)) {
                $wStart = $cur->copy()->max($dateFrom);
                $wEnd   = $cur->copy()->endOfWeek()->min($dateTo);
                $data   = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->where('kegiatan_id', $kegiatan_id)
                    ->whereBetween('tanggal', [$wStart->format('Y-m-d'), $wEnd->format('Y-m-d')])
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();
                $trendData[] = [
                    'label' => $wStart->format('d/m') . '–' . $wEnd->format('d/m'),
                    'hadir' => ($data['Hadir'] ?? 0) + ($data['Terlambat'] ?? 0),
                    'total' => array_sum($data),
                ];
                $cur->addWeek();
            }
            $trendLabel = 'Mingguan';
        }

        $fromTab = $request->input('from_tab', 'jadwal');

        return view('santri.kegiatan.show', compact(
            'santri', 'kegiatan', 'riwayats',
            'stats', 'totalAbsensi', 'hadirEfektif', 'persentaseHadir',
            'trendData', 'trendLabel',
            'dateFrom', 'dateTo', 'fromStr', 'toStr', 'preset', 'fromTab',
            'absensiByDate'
        ));
    }
}