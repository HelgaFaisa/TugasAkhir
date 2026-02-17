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
        // Tentukan tanggal yang dipilih (default: hari ini, tapi bisa pilih tanggal lain)
        $selectedDate = $request->filled('tanggal') 
            ? Carbon::parse($request->tanggal) 
            : Carbon::now();
        
        $hariIndonesia = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Ahad'
        ];
        
        $selectedHari = $hariIndonesia[$selectedDate->format('l')];
        
        // Filter kelas (optional)
        $selectedKelasId = $request->filled('kelas') ? $request->kelas : null;
        
        // Query kegiatan hari yang dipilih
        $query = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok', 'absensis' => function($q) use ($selectedDate) {
            $q->whereDate('tanggal', $selectedDate->format('Y-m-d'));
        }])->where('hari', $selectedHari);
        
        // Filter by kelas if selected
        if ($selectedKelasId) {
            if ($selectedKelasId === 'umum') {
                // Kegiatan umum (tidak punya relasi kelas)
                $query->doesntHave('kelasKegiatan');
            } else {
                // Kegiatan untuk kelas tertentu
                $query->whereHas('kelasKegiatan', function($q) use ($selectedKelasId) {
                    $q->where('kelas.id', $selectedKelasId);
                });
            }
        }
        
        $kegiatanHariIni = $query->orderBy('waktu_mulai')->get();
        
        // Total santri aktif (untuk perhitungan %)
        $totalSantriAktif = Santri::where('status', 'Aktif')->count();
        
        // Hitung statistik untuk setiap kegiatan
        $kegiatanHariIni->each(function ($kegiatan) use ($totalSantriAktif, $selectedDate) {
            $totalAbsensi = $kegiatan->absensis->count();
            $hadir = $kegiatan->absensis->where('status', 'Hadir')->count();
            
            // Persentase kehadiran
            $persenKehadiran = $totalAbsensi > 0 ? round(($hadir / $totalAbsensi) * 100) : 0;
            
            // Status kegiatan berdasarkan waktu
            $now = Carbon::now();
            $waktuMulaiStr = is_string($kegiatan->waktu_mulai) 
                ? $kegiatan->waktu_mulai 
                : $kegiatan->waktu_mulai->format('H:i');
            $waktuSelesaiStr = is_string($kegiatan->waktu_selesai) 
                ? $kegiatan->waktu_selesai 
                : $kegiatan->waktu_selesai->format('H:i');
            
            $waktuMulai = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $waktuMulaiStr);
            $waktuSelesai = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $waktuSelesaiStr);
            
            if ($selectedDate->isToday()) {
                if ($now->lt($waktuMulai)) {
                    $status = 'belum';
                } elseif ($now->between($waktuMulai, $waktuSelesai)) {
                    $status = 'berlangsung';
                } else {
                    $status = 'selesai';
                }
            } elseif ($selectedDate->isFuture()) {
                $status = 'belum';
            } else {
                $status = 'selesai';
            }
            
            // Tambahkan data ke object
            $kegiatan->total_hadir = $hadir;
            $kegiatan->total_absensi = $totalAbsensi;
            $kegiatan->persen_kehadiran = $persenKehadiran;
            $kegiatan->status_kegiatan = $status;
        });
        
        // KPI Cards
        $totalKegiatanHariIni = $kegiatanHariIni->count();
        $kegiatanSelesai = $kegiatanHariIni->where('status_kegiatan', 'selesai')->count();
        $kegiatanBerlangsung = $kegiatanHariIni->where('status_kegiatan', 'berlangsung')->count();
        $avgKehadiran = $kegiatanHariIni->count() > 0 
            ? round($kegiatanHariIni->avg('persen_kehadiran')) 
            : 0;
        
        // KPI Comparison vs minggu lalu (same day)
        $lastWeekDate = $selectedDate->copy()->subWeek();
        $lastWeekHari = $hariIndonesia[$lastWeekDate->format('l')];
        
        $kegiatanLastWeek = Kegiatan::where('hari', $lastWeekHari)->count();
        $comparisonTotal = $totalKegiatanHariIni - $kegiatanLastWeek;
        
        // Avg kehadiran minggu lalu
        $avgKehadiranLastWeek = Cache::remember('avg_kehadiran_' . $lastWeekDate->format('Y-m-d'), 600, function() use ($lastWeekDate, $lastWeekHari) {
            $kegiatanLastWeek = Kegiatan::where('hari', $lastWeekHari)->get();
            $totalPersen = 0;
            $count = 0;
            
            foreach ($kegiatanLastWeek as $kg) {
                $absensi = AbsensiKegiatan::where('kegiatan_id', $kg->kegiatan_id)
                    ->whereDate('tanggal', $lastWeekDate->format('Y-m-d'))
                    ->get();
                if ($absensi->count() > 0) {
                    $hadir = $absensi->where('status', 'Hadir')->count();
                    $totalPersen += ($hadir / $absensi->count()) * 100;
                    $count++;
                }
            }
            
            return $count > 0 ? round($totalPersen / $count) : 0;
        });
        
        $comparisonAvg = $avgKehadiran - $avgKehadiranLastWeek;
        
        // Get kelas list for filter tabs
        $kelasList = Kelas::with('kelompok')->active()->ordered()->get();
        
        // Generate Quick Insights
        $insights = $this->generateInsights($kegiatanHariIni, $totalSantriAktif, $selectedDate);
        
        // Heatmap data (30 hari terakhir) - cached
        $heatmapData = Cache::remember('heatmap_30days_' . now()->format('Y-m-d'), 600, function() {
            return $this->generateHeatmapData();
        });
        
        // Data untuk view
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        
        return view('admin.kegiatan.data.dashboard', compact(
            'kegiatanHariIni',
            'totalKegiatanHariIni',
            'kegiatanSelesai',
            'kegiatanBerlangsung',
            'avgKehadiran',
            'totalSantriAktif',
            'selectedDate',
            'selectedHari',
            'hariList',
            'kelasList',
            'selectedKelasId',
            'comparisonTotal',
            'comparisonAvg',
            'insights',
            'heatmapData'
        ));
    }
    
    /**
     * Generate Quick Insights (Rule-Based AI)
     */
    private function generateInsights($kegiatanHariIni, $totalSantriAktif, $selectedDate)
    {
        $insights = [];
        
        // Rule 1: Kehadiran rendah (<70%)
        foreach ($kegiatanHariIni as $kegiatan) {
            if ($kegiatan->total_absensi > 0 && $kegiatan->persen_kehadiran < 70) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'message' => "Kegiatan {$kegiatan->nama_kegiatan} kehadiran rendah ({$kegiatan->persen_kehadiran}%)",
                    'detail' => "{$kegiatan->total_hadir} dari {$kegiatan->total_absensi} santri hadir",
                    'action_url' => route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) . '?tanggal=' . $selectedDate->format('Y-m-d'),
                    'action_text' => 'Input Absensi'
                ];
            }
        }
        
        // Rule 2: Kehadiran perfect (100%)
        foreach ($kegiatanHariIni as $kegiatan) {
            if ($kegiatan->persen_kehadiran == 100 && $kegiatan->total_absensi > 0) {
                $insights[] = [
                    'type' => 'success',
                    'icon' => 'check-circle',
                    'message' => "Perfect! {$kegiatan->nama_kegiatan} kehadiran 100%",
                    'detail' => 'Semua santri hadir',
                    'action_url' => null,
                    'action_text' => null
                ];
            }
        }
        
        // Rule 3: Kegiatan sedang berlangsung
        $kegiatanLive = $kegiatanHariIni->where('status_kegiatan', 'berlangsung')->first();
        if ($kegiatanLive) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'clock',
                'message' => "Kegiatan {$kegiatanLive->nama_kegiatan} sedang berlangsung",
                'detail' => "Progress absensi: {$kegiatanLive->persen_kehadiran}%",
                'action_url' => route('admin.absensi-kegiatan.input', $kegiatanLive->kegiatan_id) . '?tanggal=' . $selectedDate->format('Y-m-d'),
                'action_text' => 'Input Absensi Sekarang'
            ];
        }
        
        // Rule 4: Kegiatan selesai tapi belum input absensi
        foreach ($kegiatanHariIni as $kegiatan) {
            if ($kegiatan->status_kegiatan == 'selesai' && $kegiatan->total_absensi == 0) {
                $waktuSelesai = is_string($kegiatan->waktu_selesai) 
                    ? $kegiatan->waktu_selesai 
                    : $kegiatan->waktu_selesai->format('H:i');
                    
                $insights[] = [
                    'type' => 'danger',
                    'icon' => 'exclamation-circle',
                    'message' => "Kegiatan {$kegiatan->nama_kegiatan} belum input absensi",
                    'detail' => "Sudah selesai pukul {$waktuSelesai}",
                    'action_url' => route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) . '?tanggal=' . $selectedDate->format('Y-m-d'),
                    'action_text' => 'Input Sekarang'
                ];
            }
        }
        
        return collect($insights)->take(5)->toArray(); // Max 5 insights
    }
    
    /**
     * Generate Heatmap Data (30 hari terakhir)
     */
    private function generateHeatmapData()
    {
        $heatmapData = [];
        $startDate = Carbon::now()->subDays(29);
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            
            // Hitung rata-rata kehadiran hari tersebut
            $absensi = AbsensiKegiatan::whereDate('tanggal', $dateStr)->get();
            
            if ($absensi->count() > 0) {
                $hadir = $absensi->where('status', 'Hadir')->count();
                $percentage = round(($hadir / $absensi->count()) * 100, 1);
            } else {
                $percentage = 0;
            }
            
            $heatmapData[] = [
                'date' => $dateStr,
                'day_name' => $date->locale('id')->isoFormat('ddd'),
                'percentage' => $percentage,
                'level' => $this->getHeatmapLevel($percentage),
                'is_today' => $date->isToday()
            ];
        }
        
        return $heatmapData;
    }
    
    /**
     * Get Heatmap Level (0-4)
     */
    private function getHeatmapLevel($percentage)
    {
        if ($percentage >= 90) return 4; // Dark green
        if ($percentage >= 80) return 3; // Green
        if ($percentage >= 70) return 2; // Yellow
        if ($percentage > 0) return 1; // Red
        return 0; // No data
    }
    
    /**
     * AJAX: Get Detail Kegiatan untuk Modal
     */
    public function getDetailModal($kegiatan_id, Request $request)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok'])
            ->where('kegiatan_id', $kegiatan_id)
            ->firstOrFail();
        
        // Get absensi untuk tanggal tersebut
        $absensis = AbsensiKegiatan::with('santri')
            ->where('kegiatan_id', $kegiatan_id)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('waktu_absen', 'desc')
            ->get();
        
        // Statistik
        $stats = [
            'hadir' => $absensis->where('status', 'Hadir')->count(),
            'izin' => $absensis->where('status', 'Izin')->count(),
            'sakit' => $absensis->where('status', 'Sakit')->count(),
            'alpa' => $absensis->where('status', 'Alpa')->count(),
        ];
        
        // Total santri yang seharusnya
        if ($kegiatan->isForAllClasses()) {
            $totalSantri = Santri::where('status', 'Aktif')->count();
        } else {
            $totalSantri = $kegiatan->getEligibleSantris()->count();
        }
        
        $stats['belum_absen'] = $totalSantri - $absensis->count();
        $stats['total'] = $totalSantri;
        $stats['persen_hadir'] = $totalSantri > 0 ? round(($stats['hadir'] / $totalSantri) * 100, 1) : 0;
        
        return view('admin.kegiatan.data.partials.detail-modal', compact('kegiatan', 'absensis', 'stats', 'tanggal'));
    }
    
    /**
     * Jadwal Kegiatan Lengkap (untuk "Lihat Semua Jadwal")
     */
    public function jadwal(Request $request)
    {
        $query = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok']);

        // Filter hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter kelas
        if ($request->filled('kelas_id')) {
            if ($request->kelas_id === 'umum') {
                $query->doesntHave('kelasKegiatan');
            } else {
                $query->whereHas('kelasKegiatan', function($q) use ($request) {
                    $q->where('kelas.id', $request->kelas_id);
                });
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $kegiatans = $query->select('id', 'kegiatan_id', 'kategori_id', 'nama_kegiatan', 'hari', 'waktu_mulai', 'waktu_selesai', 'materi')
            ->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->paginate(15)
            ->appends(request()->query());

        // Data untuk filter
        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
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
            $num = $last ? intval(substr($last->kegiatan_id, 2)) + 1 : 1;
            return 'KG' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });

        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $kelompokKelas = KelompokKelas::with(['kelas' => function($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])->active()->ordered()->get();

        return view('admin.kegiatan.data.create', compact('nextId', 'kategoris', 'hariList', 'kelompokKelas'));
    }

    /**
     * Simpan kegiatan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_kegiatans,kategori_id',
            'nama_kegiatan' => 'required|string|max:150',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'materi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'hari.required' => 'Hari wajib dipilih.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus lebih dari waktu mulai.',
        ]);

        $kegiatan = Kegiatan::create($validated);
        
        // Assign kelas to kegiatan if selected
        if ($request->has('kelas_ids') && !empty($request->kelas_ids)) {
            $kegiatan->assignKelas($request->kelas_ids);
        }
        
        Cache::forget('next_kegiatan_id');

        return redirect()->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
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
        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $kelompokKelas = KelompokKelas::with(['kelas' => function($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])->active()->ordered()->get();
        
        // Load existing kelas relations
        $kegiatan->load('kelasKegiatan');

        return view('admin.kegiatan.data.edit', compact('kegiatan', 'kategoris', 'hariList', 'kelompokKelas'));
    }

    /**
     * Update kegiatan
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_kegiatans,kategori_id',
            'nama_kegiatan' => 'required|string|max:150',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'materi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus lebih dari waktu mulai.',
        ]);

        $kegiatan->update($validated);
        
        // Update kelas assignments
        if ($request->has('kelas_ids')) {
            $kegiatan->assignKelas($request->kelas_ids ?? []);
        }

        return redirect()->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus kegiatan
     */
    public function destroy(Kegiatan $kegiatan)
    {
        $nama = $kegiatan->nama_kegiatan;
        $kegiatan->delete();
        Cache::forget('next_kegiatan_id');

        return redirect()->route('admin.kegiatan.index')
            ->with('success', "Kegiatan \"$nama\" berhasil dihapus.");
    }
}