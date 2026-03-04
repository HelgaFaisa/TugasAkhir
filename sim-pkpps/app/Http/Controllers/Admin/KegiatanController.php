<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\KategoriKegiatan;
use App\Models\KelompokKelas;
use App\Models\Kelas;
use App\Models\AbsensiKegiatan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class KegiatanController extends Controller
{
    /**
     * Dashboard Kegiatan Hari Ini (ENHANCED)
     */
    public function index(Request $request)
    {
        $selectedDate = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::now();

        $hariIndonesia = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Ahad',
        ];

        $selectedHari    = $hariIndonesia[$selectedDate->format('l')];
        $selectedKelasId = $request->filled('kelas') ? $request->kelas : null;

        $query = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok', 'absensis' => function ($q) use ($selectedDate) {
            $q->whereDate('tanggal', $selectedDate->format('Y-m-d'));
        }])->where('hari', $selectedHari);

        if ($selectedKelasId) {
            if ($selectedKelasId === 'umum') {
                $query->doesntHave('kelasKegiatan');
            } else {
                $query->whereHas('kelasKegiatan', function ($q) use ($selectedKelasId) {
                    $q->where('kelas.id', $selectedKelasId);
                });
            }
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $kegiatanHariIni  = $query->orderBy('waktu_mulai')->get();
        $totalSantriAktif = Santri::where('status', 'Aktif')->count();

        $kegiatanHariIni->each(function ($kegiatan) use ($totalSantriAktif, $selectedDate) {
            $totalAbsensi    = $kegiatan->absensis->count();
            $hadir           = $kegiatan->absensis->where('status', 'Hadir')->count();
            $persenKehadiran = $totalAbsensi > 0 ? round(($hadir / $totalAbsensi) * 100) : 0;

            $now             = Carbon::now();
            $waktuMulaiStr   = is_string($kegiatan->waktu_mulai)   ? $kegiatan->waktu_mulai   : $kegiatan->waktu_mulai->format('H:i');
            $waktuSelesaiStr = is_string($kegiatan->waktu_selesai) ? $kegiatan->waktu_selesai : $kegiatan->waktu_selesai->format('H:i');
            $waktuMulai      = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $waktuMulaiStr);
            $waktuSelesai    = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $waktuSelesaiStr);

            if ($selectedDate->isToday()) {
                if ($now->lt($waktuMulai))                        $status = 'belum';
                elseif ($now->between($waktuMulai, $waktuSelesai)) $status = 'berlangsung';
                else                                               $status = 'selesai';
            } elseif ($selectedDate->isFuture()) {
                $status = 'belum';
            } else {
                $status = 'selesai';
            }

            $kegiatan->total_hadir      = $hadir;
            $kegiatan->total_absensi    = $totalAbsensi;
            $kegiatan->persen_kehadiran = $persenKehadiran;
            $kegiatan->status_kegiatan  = $status;
        });

        $totalKegiatanHariIni = $kegiatanHariIni->count();
        $kegiatanSelesai      = $kegiatanHariIni->where('status_kegiatan', 'selesai')->count();
        $kegiatanBerlangsung  = $kegiatanHariIni->where('status_kegiatan', 'berlangsung')->count();
        $avgKehadiran         = $kegiatanHariIni->count() > 0 ? round($kegiatanHariIni->avg('persen_kehadiran')) : 0;

        $lastWeekDate         = $selectedDate->copy()->subWeek();
        $lastWeekHari         = $hariIndonesia[$lastWeekDate->format('l')];
        $kegiatanLastWeekCount = Kegiatan::where('hari', $lastWeekHari)->count();
        $comparisonTotal      = $totalKegiatanHariIni - $kegiatanLastWeekCount;

        $avgKehadiranLastWeek = Cache::remember('avg_kehadiran_' . $lastWeekDate->format('Y-m-d'), 600, function () use ($lastWeekDate, $lastWeekHari) {
            $list        = Kegiatan::where('hari', $lastWeekHari)->get();
            $totalPersen = 0;
            $count       = 0;
            foreach ($list as $kg) {
                $abs = AbsensiKegiatan::where('kegiatan_id', $kg->kegiatan_id)
                    ->whereDate('tanggal', $lastWeekDate->format('Y-m-d'))->get();
                if ($abs->count() > 0) {
                    $totalPersen += ($abs->where('status', 'Hadir')->count() / $abs->count()) * 100;
                    $count++;
                }
            }
            return $count > 0 ? round($totalPersen / $count) : 0;
        });

        $comparisonAvg = $avgKehadiran - $avgKehadiranLastWeek;
        $kelasList     = Kelas::with('kelompok')->active()->ordered()->get();
        $kategoris     = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $insights      = $this->generateInsights($kegiatanHariIni, $totalSantriAktif, $selectedDate);

        $heatmapData = Cache::remember('heatmap_30days_' . now()->format('Y-m-d'), 600, function () {
            return $this->generateHeatmapData();
        });

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];

        return view('admin.kegiatan.data.dashboard', compact(
            'kegiatanHariIni', 'totalKegiatanHariIni', 'kegiatanSelesai',
            'kegiatanBerlangsung', 'avgKehadiran', 'totalSantriAktif',
            'selectedDate', 'selectedHari', 'hariList', 'kelasList',
            'selectedKelasId', 'comparisonTotal', 'comparisonAvg',
            'insights', 'heatmapData', 'kategoris'
        ));
    }

    /**
     * Generate Quick Insights (Rule-Based AI)
     */
    private function generateInsights($kegiatanHariIni, $totalSantriAktif, $selectedDate)
    {
        $insights = [];

        foreach ($kegiatanHariIni as $kegiatan) {
            if ($kegiatan->total_absensi > 0 && $kegiatan->persen_kehadiran < 70) {
                $insights[] = [
                    'type' => 'warning', 'icon' => 'exclamation-triangle',
                    'message'     => "Kegiatan {$kegiatan->nama_kegiatan} kehadiran rendah ({$kegiatan->persen_kehadiran}%)",
                    'detail'      => "{$kegiatan->total_hadir} dari {$kegiatan->total_absensi} santri hadir",
                    'action_url'  => route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) . '?tanggal=' . $selectedDate->format('Y-m-d'),
                    'action_text' => 'Input Absensi',
                ];
            }
        }

        foreach ($kegiatanHariIni as $kegiatan) {
            if ($kegiatan->persen_kehadiran == 100 && $kegiatan->total_absensi > 0) {
                $insights[] = [
                    'type' => 'success', 'icon' => 'check-circle',
                    'message' => "Perfect! {$kegiatan->nama_kegiatan} kehadiran 100%",
                    'detail'  => 'Semua santri hadir', 'action_url' => null, 'action_text' => null,
                ];
            }
        }

        $kegiatanLive = $kegiatanHariIni->where('status_kegiatan', 'berlangsung')->first();
        if ($kegiatanLive) {
            $insights[] = [
                'type' => 'info', 'icon' => 'clock',
                'message'     => "Kegiatan {$kegiatanLive->nama_kegiatan} sedang berlangsung",
                'detail'      => "Progress absensi: {$kegiatanLive->persen_kehadiran}%",
                'action_url'  => route('admin.absensi-kegiatan.input', $kegiatanLive->kegiatan_id) . '?tanggal=' . $selectedDate->format('Y-m-d'),
                'action_text' => 'Input Absensi Sekarang',
            ];
        }

        foreach ($kegiatanHariIni as $kegiatan) {
            if ($kegiatan->status_kegiatan == 'selesai' && $kegiatan->total_absensi == 0) {
                $waktuSelesai = is_string($kegiatan->waktu_selesai) ? $kegiatan->waktu_selesai : $kegiatan->waktu_selesai->format('H:i');
                $insights[] = [
                    'type' => 'danger', 'icon' => 'exclamation-circle',
                    'message'     => "Kegiatan {$kegiatan->nama_kegiatan} belum input absensi",
                    'detail'      => "Sudah selesai pukul {$waktuSelesai}",
                    'action_url'  => route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) . '?tanggal=' . $selectedDate->format('Y-m-d'),
                    'action_text' => 'Input Sekarang',
                ];
            }
        }

        return collect($insights)->take(5)->toArray();
    }

    /**
     * Generate Heatmap Data (30 hari terakhir)
     */
    private function generateHeatmapData()
    {
        $heatmapData = [];
        $startDate   = Carbon::now()->subDays(29);

        for ($i = 0; $i < 30; $i++) {
            $date    = $startDate->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $absensi = AbsensiKegiatan::whereDate('tanggal', $dateStr)->get();

            $percentage = $absensi->count() > 0
                ? round(($absensi->where('status', 'Hadir')->count() / $absensi->count()) * 100, 1)
                : 0;

            $heatmapData[] = [
                'date'       => $dateStr,
                'day_name'   => $date->locale('id')->isoFormat('ddd'),
                'percentage' => $percentage,
                'level'      => $this->getHeatmapLevel($percentage),
                'is_today'   => $date->isToday(),
            ];
        }

        return $heatmapData;
    }

    private function getHeatmapLevel($percentage)
    {
        if ($percentage >= 90) return 4;
        if ($percentage >= 80) return 3;
        if ($percentage >= 70) return 2;
        if ($percentage > 0)  return 1;
        return 0;
    }

    /**
     * AJAX: Get Detail Kegiatan untuk Modal
     */
    public function getDetailModal($kegiatan_id, Request $request)
    {
        $tanggal  = $request->get('tanggal', now()->format('Y-m-d'));
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok'])
            ->where('kegiatan_id', $kegiatan_id)->firstOrFail();

        $absensis = AbsensiKegiatan::with(['santri.kelasSantri.kelas'])
            ->where('kegiatan_id', $kegiatan_id)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('waktu_absen', 'desc')->get();

        $isUmum = $kegiatan->isForAllClasses();

        // Grup absensi per kelas kegiatan (khusus) atau kelas_name (umum)
        if ($isUmum) {
            $absensiPerKelas = $absensis->groupBy(fn($item) => $item->santri->kelas_name ?? 'Belum Ada Kelas')->sortKeys();
        } else {
            $absensiPerKelas = collect();
            foreach ($kegiatan->kelasKegiatan as $kelas) {
                $filtered = $absensis->filter(fn($item) => $item->santri->kelasSantri->contains('id_kelas', $kelas->id));
                if ($filtered->count() > 0) $absensiPerKelas[$kelas->nama_kelas] = $filtered;
            }
            // Sisanya yang tidak cocok kelas manapun
            $placedIds = $absensiPerKelas->flatten()->pluck('id')->toArray();
            $lainnya = $absensis->filter(fn($item) => !$absensiPerKelas->flatten()->contains('id', $item->id));
            if ($lainnya->count() > 0) $absensiPerKelas['Kelas Lain'] = $lainnya;
        }

        $stats       = [
            'hadir'     => $absensis->where('status', 'Hadir')->count(),
            'terlambat' => $absensis->where('status', 'Terlambat')->count(),
            'izin'      => $absensis->where('status', 'Izin')->count(),
            'sakit'     => $absensis->where('status', 'Sakit')->count(),
            'alpa'      => $absensis->where('status', 'Alpa')->count(),
        ];
        $totalSantri = Santri::where('status', 'Aktif')->count();

        $stats['belum_absen']  = max(0, $totalSantri - $absensis->count());
        $stats['sudah_absen']  = $absensis->count();
        $stats['total']        = $totalSantri;
        $stats['persen_hadir'] = $totalSantri > 0 ? round(($stats['hadir'] / $totalSantri) * 100, 1) : 0;

        // Daftar santri belum absen, di-group per kelas kegiatan (khusus) atau kelasPrimary (umum)
        $idSantriSudahAbsen = $absensis->pluck('id_santri')->toArray();
        $allBelumAbsen = Santri::where('status', 'Aktif')
            ->whereNotIn('id_santri', $idSantriSudahAbsen)
            ->with(['kelasSantri.kelas', 'kelasPrimary.kelas'])
            ->orderBy('nama_lengkap')
            ->get();

        if ($isUmum) {
            $santriBelumAbsenPerKelas = $allBelumAbsen->groupBy(function($s) {
                return optional(optional($s->kelasPrimary)->kelas)->nama_kelas ?? 'Tanpa Kelas';
            })->sortKeys();
        } else {
            $santriBelumAbsenPerKelas = collect();
            $placedBelumIds = [];
            foreach ($kegiatan->kelasKegiatan as $kelas) {
                $inKelas = $allBelumAbsen->filter(function($s) use ($kelas, &$placedBelumIds) {
                    if (in_array($s->id_santri, $placedBelumIds)) return false;
                    return $s->kelasSantri->contains('id_kelas', $kelas->id);
                });
                foreach ($inKelas as $s) $placedBelumIds[] = $s->id_santri;
                if ($inKelas->count() > 0) $santriBelumAbsenPerKelas[$kelas->nama_kelas] = $inKelas;
            }
            $lainnyaBelum = $allBelumAbsen->whereNotIn('id_santri', $placedBelumIds);
            if ($lainnyaBelum->count() > 0) $santriBelumAbsenPerKelas['Kelas Lain'] = $lainnyaBelum;
        }

        $santriBelumAbsen = $allBelumAbsen; // kept for count reference

        return view('admin.kegiatan.data.partials.detail-modal', compact('kegiatan', 'absensis', 'absensiPerKelas', 'stats', 'tanggal', 'santriBelumAbsen', 'santriBelumAbsenPerKelas'));
    }

    /**
     * Jadwal Kegiatan Lengkap
     */
    public function jadwal(Request $request)
    {
        $query = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok']);

        if ($request->filled('hari'))       $query->where('hari', $request->hari);
        if ($request->filled('kategori_id')) $query->where('kategori_id', $request->kategori_id);
        if ($request->filled('kelas_id')) {
            if ($request->kelas_id === 'umum') {
                $query->doesntHave('kelasKegiatan');
            } else {
                $query->whereHas('kelasKegiatan', fn($q) => $q->where('kelas.id', $request->kelas_id));
            }
        }
        if ($request->filled('search')) $query->search($request->search);

        $kegiatans = $query->select('id', 'kegiatan_id', 'kategori_id', 'nama_kegiatan', 'hari', 'waktu_mulai', 'waktu_selesai', 'materi')
            ->orderBy('hari')->orderBy('waktu_mulai')
            ->paginate(15)->appends(request()->query());

        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList  = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $kelasList = Kelas::with('kelompok')->active()->ordered()->get();

        return view('admin.kegiatan.data.index', compact('kegiatans', 'kategoris', 'hariList', 'kelasList'));
    }

    /**
     * Form tambah kegiatan
     */
    public function create()
    {
        $nextId = Cache::remember('next_kegiatan_id', 60, function () {
            $last = Kegiatan::select('kegiatan_id')->orderBy('id', 'desc')->first();
            $num  = $last ? intval(substr($last->kegiatan_id, 2)) + 1 : 1;
            return 'KG' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });

        $kategoris     = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList      = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $kelompokKelas = KelompokKelas::with(['kelas' => fn($q) => $q->where('is_active', true)->orderBy('urutan')])
            ->active()->ordered()->get();

        return view('admin.kegiatan.data.create', compact('nextId', 'kategoris', 'hariList', 'kelompokKelas'));
    }

    /**
     * Simpan kegiatan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id'   => 'required|exists:kategori_kegiatans,kategori_id',
            'nama_kegiatan' => 'required|string|max:150',
            'hari'          => 'required|array|min:1',
            'hari.*'        => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'materi'        => 'nullable|string|max:200',
            'keterangan'    => 'nullable|string',
            'kelas_ids'     => 'nullable|array',
            'kelas_ids.*'   => 'exists:kelas,id',
        ], [
            'kategori_id.required'   => 'Kategori wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'hari.required'          => 'Minimal pilih satu hari.',
            'hari.min'               => 'Minimal pilih satu hari.',
            'waktu_mulai.required'   => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after'    => 'Waktu selesai harus lebih dari waktu mulai.',
        ]);

        $hariList = $validated['hari'];
        unset($validated['hari']);
        $createdCount = 0;

        foreach ($hariList as $hari) {
            $kg = Kegiatan::create(array_merge($validated, ['hari' => $hari]));
            if ($request->has('kelas_ids') && !empty($request->kelas_ids)) {
                $kg->assignKelas($request->kelas_ids);
            }
            $createdCount++;
        }

        Cache::forget('next_kegiatan_id');

        $message = $createdCount > 1
            ? "Berhasil menambahkan kegiatan untuk {$createdCount} hari."
            : 'Kegiatan berhasil ditambahkan.';

        return redirect()->route('admin.kegiatan.jadwal')->with('success', $message);
    }

    /**
     * Tampilkan detail kegiatan
     */
    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load(['kategori', 'kelasKegiatan.kelompok']);
        return view('admin.kegiatan.data.show', compact('kegiatan'));
    }

    /**
     * Form edit kegiatan
     */
    public function edit(Kegiatan $kegiatan)
    {
        $kategoris     = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList      = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $kelompokKelas = KelompokKelas::with(['kelas' => fn($q) => $q->where('is_active', true)->orderBy('urutan')])
            ->active()->ordered()->get();

        $kegiatan->load('kelasKegiatan');

        return view('admin.kegiatan.data.edit', compact('kegiatan', 'kategoris', 'hariList', 'kelompokKelas'));
    }

    /**
     * Update kegiatan — smart multi-hari
     *
     * Logika:
     * - Cari semua kegiatan "saudara" = nama_kegiatan + kategori_id LAMA yang sama
     * - Hari yang DIPILIH & sudah ada di saudara → UPDATE kegiatan saudara tsb
     * - Hari yang DIPILIH tapi belum ada di saudara → BUAT kegiatan baru
     * - Hari yang TIDAK DIPILIH → tidak disentuh sama sekali
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'kategori_id'   => 'required|exists:kategori_kegiatans,kategori_id',
            'nama_kegiatan' => 'required|string|max:150',
            'hari'          => 'required|array|min:1',
            'hari.*'        => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'materi'        => 'nullable|string|max:200',
            'keterangan'    => 'nullable|string',
            'kelas_ids'     => 'nullable|array',
            'kelas_ids.*'   => 'exists:kelas,id',
        ], [
            'kategori_id.required'   => 'Kategori wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'hari.required'          => 'Minimal pilih satu hari.',
            'hari.min'               => 'Minimal pilih satu hari.',
            'waktu_selesai.after'    => 'Waktu selesai harus lebih dari waktu mulai.',
        ]);

        $hariDipilih  = $validated['hari'];
        $kelasIds     = $request->input('kelas_ids', []);

        // Data dasar tanpa hari & kelas_ids
        $baseData = collect($validated)->except(['hari', 'kelas_ids'])->toArray();

        // Cari semua saudara berdasarkan nama + kategori LAMA (sebelum diubah)
        $saudara = Kegiatan::where('nama_kegiatan', $kegiatan->nama_kegiatan)
            ->where('kategori_id', $kegiatan->kategori_id)
            ->get()
            ->keyBy('hari'); // ['Senin' => obj, 'Rabu' => obj, ...]

        $updatedCount = 0;
        $createdCount = 0;

        foreach ($hariDipilih as $hari) {
            if ($saudara->has($hari)) {
                // Kegiatan di hari ini sudah ada → update
                $target = $saudara->get($hari);
                $target->update(array_merge($baseData, ['hari' => $hari]));
                $target->assignKelas($kelasIds);
                $updatedCount++;
            } else {
                // Belum ada kegiatan di hari ini → buat baru
                $newKg = Kegiatan::create(array_merge($baseData, ['hari' => $hari]));
                $newKg->assignKelas($kelasIds);
                $createdCount++;
            }
        }

        Cache::forget('next_kegiatan_id');

        $parts = [];
        if ($updatedCount > 0) $parts[] = "{$updatedCount} kegiatan diperbarui";
        if ($createdCount > 0) $parts[] = "{$createdCount} kegiatan baru dibuat";

        return redirect()->route('admin.kegiatan.jadwal')
            ->with('success', 'Berhasil: ' . implode(', ', $parts) . '.');
    }

    /**
     * Hapus kegiatan
     */
    public function destroy(Kegiatan $kegiatan)
    {
        $nama = $kegiatan->nama_kegiatan;
        $kegiatan->delete();
        Cache::forget('next_kegiatan_id');

        return redirect()->route('admin.kegiatan.jadwal')
            ->with('success', "Kegiatan \"{$nama}\" berhasil dihapus.");
    }
}