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

    /**
     * Resolve date range.
     * Jadwal & Riwayat default: today
     * Statistik default: this_week
     */
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
                // custom
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

    public function index(Request $request)
    {
        $idSantri = $this->getSantriId();

        // ✅ FIX: No 'kelas' column, use relasi
        $santri = Santri::where('id_santri', $idSantri)
            ->with(['kelasPrimary.kelas'])
            ->select('id_santri', 'nama_lengkap', 'nis', 'status')
            ->firstOrFail();

        $namaKelas     = optional(optional($santri->kelasPrimary)->kelas)->nama_kelas ?? '-';
        $kelasSantriId = optional($santri->kelasPrimary)->id_kelas;

        // -- Aktif tab (dari request, default: statistik) --
        $activeTab = $request->input('tab', 'statistik');

        // -- Tiap tab punya preset/range masing-masing --
        // Statistik: default this_week
        // Jadwal & Riwayat: default today
        // Request bisa bawa preset_stat, preset_jadwal, preset_riwayat
        // atau preset global (backward compat)

        // Statistik range
        $statPresetReq = $request->input('preset_stat', $request->input('preset', 'this_week'));
        [$statFrom, $statTo, $statPreset] = $this->resolveDateRange(
            $request->merge(['preset' => $statPresetReq,
                             'date_from' => $request->input('stat_date_from'),
                             'date_to'   => $request->input('stat_date_to')]),
            'this_week'
        );
        if ($statPreset === 'custom') {
            $statFrom = $request->filled('stat_date_from') ? Carbon::parse($request->stat_date_from)->startOfDay() : $statFrom;
            $statTo   = $request->filled('stat_date_to')   ? Carbon::parse($request->stat_date_to)->endOfDay()     : $statTo;
        }

        // Jadwal range
        $jadPresetReq = $request->input('preset_jad', $request->input('preset', 'today'));
        [$jadFrom, $jadTo, $jadPreset] = $this->resolveDateRange(
            $request->merge(['preset' => $jadPresetReq,
                             'date_from' => $request->input('jad_date_from'),
                             'date_to'   => $request->input('jad_date_to')]),
            'today'
        );

        // Riwayat range
        $riwPresetReq = $request->input('preset_riw', $request->input('preset', 'today'));
        [$riwFrom, $riwTo, $riwPreset] = $this->resolveDateRange(
            $request->merge(['preset' => $riwPresetReq,
                             'date_from' => $request->input('riw_date_from'),
                             'date_to'   => $request->input('riw_date_to')]),
            'today'
        );

        // -- Mapping hari --
        $hariMapDb = [
            'Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu',
            'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu',
            'Minggu' => 'Ahad',
        ];
        $hariCarbon = Carbon::now()->locale('id')->dayName;
        $hariIni    = $hariMapDb[$hariCarbon] ?? $hariCarbon;

        // ── KPI stats (pakai stat range) ──────────────────────────────────
        $statFromStr = $statFrom->format('Y-m-d');
        $statToStr   = $statTo->format('Y-m-d');

        $statsRange = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereBetween('tanggal', [$statFromStr, $statToStr])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalRange          = array_sum($statsRange);
        $hadirRange          = $statsRange['Hadir'] ?? 0;
        $izinRange           = $statsRange['Izin']  ?? 0;
        $sakitRange          = $statsRange['Sakit']  ?? 0;
        $alpaRange           = $statsRange['Alpa']  ?? 0;
        $persentaseKehadiran = $totalRange > 0 ? round($hadirRange / $totalRange * 100, 1) : 0;

        // ── JADWAL ────────────────────────────────────────────────────────
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
            ->where(function ($q) use ($kelasSantriId) {
                $q->doesntHave('kelasKegiatan')
                  ->orWhereHas('kelasKegiatan', function ($q2) use ($kelasSantriId) {
                      if ($kelasSantriId) {
                          $q2->where('kelas.id', $kelasSantriId);
                      }
                  });
            })
            ->select('kegiatan_id', 'kategori_id', 'nama_kegiatan', 'waktu_mulai', 'waktu_selesai', 'hari', 'materi')
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad')")
            ->orderBy('waktu_mulai')
            ->get();

        // Status absensi per kegiatan dalam range jadwal
        $absensiDalamRange = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereBetween('tanggal', [$jadFrom->format('Y-m-d'), $jadTo->format('Y-m-d')])
            ->pluck('status', 'kegiatan_id')
            ->toArray();

        // Status khusus hari ini (untuk badge)
        $absensiHariIni = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereDate('tanggal', Carbon::today())
            ->pluck('status', 'kegiatan_id')
            ->toArray();

        // ── RIWAYAT ───────────────────────────────────────────────────────
        $riwFromStr = $riwFrom->format('Y-m-d');
        $riwToStr   = $riwTo->format('Y-m-d');

        $queryRiwayat = AbsensiKegiatan::with('kegiatan.kategori')
            ->where('id_santri', $idSantri)
            ->whereBetween('tanggal', [$riwFromStr, $riwToStr]);

        if ($request->filled('filter_status')) {
            $queryRiwayat->where('status', $request->filter_status);
        }
        if ($request->filled('filter_kategori')) {
            $queryRiwayat->whereHas('kegiatan', fn($q) => $q->where('kategori_id', $request->filter_kategori));
        }

        $riwayats = $queryRiwayat->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->paginate(15)
            ->appends(request()->query());

        // ── STREAK ────────────────────────────────────────────────────────
        $streak = 0;
        AbsensiKegiatan::where('id_santri', $idSantri)
            ->orderByDesc('tanggal')->orderByDesc('waktu_absen')
            ->select('status')->limit(60)
            ->each(function($a) use (&$streak) {
                if ($a->status === 'Hadir') $streak++;
                else return false;
            });

        // ── GRAFIK TREN (stat range) ──────────────────────────────────────
        $diffDays   = $statFrom->diffInDays($statTo);
        $dataGrafik = [];

        if ($diffDays <= 31) {
            $cur = $statFrom->copy();
            while ($cur->lte($statTo)) {
                $d     = $cur->format('Y-m-d');
                $hadir = AbsensiKegiatan::where('id_santri', $idSantri)->whereDate('tanggal', $d)->where('status', 'Hadir')->count();
                $total = AbsensiKegiatan::where('id_santri', $idSantri)->whereDate('tanggal', $d)->count();
                $dataGrafik[] = ['label' => $cur->format('d/m'), 'hadir' => $hadir, 'total' => $total];
                $cur->addDay();
            }
        } else {
            $cur = $statFrom->copy()->startOfWeek();
            while ($cur->lte($statTo)) {
                $wStart = $cur->copy()->max($statFrom);
                $wEnd   = $cur->copy()->endOfWeek()->min($statTo);
                $hadir  = AbsensiKegiatan::where('id_santri', $idSantri)->whereBetween('tanggal', [$wStart->format('Y-m-d'), $wEnd->format('Y-m-d')])->where('status', 'Hadir')->count();
                $total  = AbsensiKegiatan::where('id_santri', $idSantri)->whereBetween('tanggal', [$wStart->format('Y-m-d'), $wEnd->format('Y-m-d')])->count();
                $dataGrafik[] = ['label' => $wStart->format('d/m') . '–' . $wEnd->format('d/m'), 'hadir' => $hadir, 'total' => $total];
                $cur->addWeek();
            }
        }

        // ── CONSISTENCY SCORE per KEGIATAN (stat range) ───────────────────
        // Score = % hadir, dengan label badge berdasarkan level
        $consistencyScores = AbsensiKegiatan::where('absensi_kegiatans.id_santri', $idSantri)
            ->whereBetween('absensi_kegiatans.tanggal', [$statFromStr, $statToStr])
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->join('kategori_kegiatans', 'kegiatans.kategori_id', '=', 'kategori_kegiatans.kategori_id')
            ->select(
                'kegiatans.kegiatan_id',
                'kegiatans.nama_kegiatan',
                'kategori_kegiatans.nama_kategori',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Alpa"  THEN 1 ELSE 0 END) as alpa'),
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status IN ("Izin","Sakit") THEN 1 ELSE 0 END) as dispensasi')
            )
            ->groupBy('kegiatans.kegiatan_id', 'kegiatans.nama_kegiatan', 'kategori_kegiatans.nama_kategori')
            ->get()
            ->map(function ($row) {
                $score = $row->total > 0 ? round($row->hadir / $row->total * 100) : 0;
                // Badge tier
                if ($score >= 90)      { $badge = 'Konsisten';       $tier = 'top'; }
                elseif ($score >= 75)  { $badge = 'Baik';            $tier = 'good'; }
                elseif ($score >= 60)  { $badge = 'Cukup';           $tier = 'fair'; }
                elseif ($score >= 40)  { $badge = 'Perlu Perhatian'; $tier = 'warn'; }
                else                   { $badge = 'Kritis';          $tier = 'crit'; }
                $row->score = $score;
                $row->badge = $badge;
                $row->tier  = $tier;
                return $row;
            })
            ->sortByDesc('score')
            ->values();

        // ── HEATMAP: kalender bulan aktif (stat range, max tampil 1 bulan) ─
        // Kita buat kalender bulan-bulan dalam stat range, dengan angka tanggal
        $heatmapMonths = [];
        $cur = $statFrom->copy()->startOfMonth();
        while ($cur->lte($statTo)) {
            $monthKey = $cur->format('Y-m');
            $daysInMonth = $cur->daysInMonth;
            $firstDayOfWeek = $cur->copy()->startOfMonth()->dayOfWeekIso; // 1=Mon..7=Sun

            $days = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = $cur->format('Y-m') . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                $rows = AbsensiKegiatan::where('id_santri', $idSantri)->whereDate('tanggal', $date)->get();
                $level = 0;
                if ($rows->count() > 0) {
                    $pct   = round($rows->where('status', 'Hadir')->count() / $rows->count() * 100);
                    $level = $pct >= 90 ? 4 : ($pct >= 70 ? 3 : ($pct >= 50 ? 2 : 1));
                }
                $days[] = [
                    'day'      => $d,
                    'date'     => $date,
                    'level'    => $level,
                    'count'    => $rows->where('status', 'Hadir')->count(),
                    'total'    => $rows->count(),
                    'is_today' => $date === Carbon::today()->format('Y-m-d'),
                    'in_range' => $date >= $statFromStr && $date <= $statToStr,
                ];
            }

            $heatmapMonths[] = [
                'label'         => $cur->locale('id')->isoFormat('MMMM YYYY'),
                'firstDayOfWeek'=> $firstDayOfWeek,
                'days'          => $days,
            ];

            $cur->addMonth();
        }

        $kategoriList = \App\Models\KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();

        return view('santri.kegiatan.index', compact(
            'santri', 'namaKelas',
            'jadwalDalamRange', 'absensiDalamRange', 'absensiHariIni', 'hariIni',
            'jadPreset', 'jadFrom', 'jadTo',
            'riwayats', 'riwPreset', 'riwFrom', 'riwTo',
            'statsRange', 'totalRange', 'hadirRange', 'izinRange', 'sakitRange', 'alpaRange',
            'persentaseKehadiran', 'streak',
            'dataGrafik', 'statPreset', 'statFrom', 'statTo', 'statFromStr', 'statToStr', 'diffDays',
            'consistencyScores',
            'heatmapMonths',
            'kategoriList',
            'activeTab', 'hariIni'
        ));
    }

    public function show($kegiatan_id, Request $request)
    {
        $idSantri = $this->getSantriId();

        $santri = Santri::where('id_santri', $idSantri)
            ->with(['kelasPrimary.kelas'])
            ->select('id_santri', 'nama_lengkap', 'nis', 'status')
            ->firstOrFail();

        $kegiatan = Kegiatan::with('kategori')
            ->where('kegiatan_id', $kegiatan_id)
            ->firstOrFail();

        $riwayats = AbsensiKegiatan::where('id_santri', $santri->id_santri)
            ->where('kegiatan_id', $kegiatan_id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        $stats = AbsensiKegiatan::where('id_santri', $santri->id_santri)
            ->where('kegiatan_id', $kegiatan_id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalAbsensi    = array_sum($stats);
        $persentaseHadir = $totalAbsensi > 0
            ? round(($stats['Hadir'] ?? 0) / $totalAbsensi * 100, 1) : 0;

        $trendBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $data  = AbsensiKegiatan::where('id_santri', $idSantri)
                ->where('kegiatan_id', $kegiatan_id)
                ->whereMonth('tanggal', $bulan->month)
                ->whereYear('tanggal', $bulan->year)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
            $trendBulanan[] = [
                'bulan' => $bulan->locale('id')->isoFormat('MMM YY'),
                'hadir' => $data['Hadir'] ?? 0,
                'total' => array_sum($data),
            ];
        }

        // Referrer tab untuk tombol kembali
        $fromTab = $request->input('from_tab', 'riwayat');

        return view('santri.kegiatan.show', compact(
            'santri', 'kegiatan', 'riwayats',
            'stats', 'totalAbsensi', 'persentaseHadir',
            'trendBulanan', 'fromTab'
        ));
    }
}