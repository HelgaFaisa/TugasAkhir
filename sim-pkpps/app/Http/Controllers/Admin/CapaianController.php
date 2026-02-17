<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Capaian;
use App\Models\Santri;
use App\Models\Materi;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\SantriKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CapaianController extends Controller
{
    /**
     * Display a listing of capaian (per santri dengan total progress)
     */
    public function index(Request $request)
    {
        // Data untuk filter
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();
        $semesterAktif = Semester::aktif()->first();
        
        // Get filter parameters
        $selectedKelas = $request->input('id_kelas');
        $selectedSemester = $request->input('id_semester', $semesterAktif?->id_semester);
        $search = $request->input('search');
        
        // Dynamic kelas list dari database
        $kelasList = Kelas::active()->ordered()->with('kelompok')->get();
        
        // Query santri dengan filter (eager load kelas untuk accessor)
        $query = Santri::where('status', 'Aktif')
            ->with(['kelasPrimary.kelas.kelompok']);
        
        // Filter berdasarkan kelas jika dipilih (by ID)
        if ($selectedKelas) {
            $query->kelas($selectedKelas);
        }
        
        // Filter berdasarkan search (nama atau NIS)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }
        
        $santris = $query->orderBy('nama_lengkap')->get();
        
        // Hitung total progress per santri
        $santriData = $santris->map(function($santri) use ($selectedSemester) {
            $capaians = Capaian::where('id_santri', $santri->id_santri)
                ->when($selectedSemester, function($q) use ($selectedSemester) {
                    $q->where('id_semester', $selectedSemester);
                })
                ->get();
            
            // Hanya hitung materi yang sudah ada progressnya (persentase > 0%)
            $capaiansBerisi = $capaians->where('persentase', '>', 0);
            $totalProgress = $capaiansBerisi->isEmpty() ? 0 : $capaiansBerisi->avg('persentase');
            $totalMateri = $capaiansBerisi->count();
            
            return [
                'santri' => $santri,
                'total_progress' => round($totalProgress, 2),
                'total_materi' => $totalMateri,
                'capaians' => $capaians
            ];
        })->sortBy('total_progress')->values();

        return view('admin.capaian.index', compact('santriData', 'semesters', 'kelasList', 'selectedKelas', 'selectedSemester', 'search'));
    }

    /**
     * Show the form for creating new capaian
     */
    public function create(Request $request)
    {
        // Get santri list
        $santris = Santri::aktif()
            ->select('id', 'id_santri', 'nis', 'nama_lengkap')
            ->with(['kelasPrimary.kelas'])
            ->orderBy('nama_lengkap')
            ->get();

        // Get semester aktif
        $semesterAktif = Semester::aktif()->first();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        // Jika ada pre-selected santri
        $selectedSantri = null;
        $materiOptions = [];
        
        if ($request->filled('id_santri')) {
            $selectedSantri = Santri::where('id_santri', $request->id_santri)
                ->with(['kelasSantri.kelas'])
                ->first();
            if ($selectedSantri) {
                // Get materi sesuai semua kelas santri (via relasi)
                $kelasNames = $selectedSantri->kelasSantri
                    ->map(fn($sk) => $sk->kelas?->nama_kelas)
                    ->filter()->unique()->toArray();
                $materiOptions = Materi::whereIn('kelas', $kelasNames ?: [''])
                    ->orderBy('kategori')
                    ->orderBy('nama_kitab')
                    ->get();
            }
        }

        return view('admin.capaian.create', compact('santris', 'semesters', 'semesterAktif', 'selectedSantri', 'materiOptions'));
    }

    /**
     * Get materi by santri kelas (AJAX)
     */
    public function getMateriByKelas(Request $request)
    {
        $santri = Santri::where('id_santri', $request->id_santri)
            ->with(['kelasSantri.kelas'])
            ->first();
        
        if (!$santri) {
            return response()->json(['error' => 'Santri tidak ditemukan'], 404);
        }

        // Get materi sesuai semua kelas santri
        $kelasNames = $santri->kelasSantri
            ->map(fn($sk) => $sk->kelas?->nama_kelas)
            ->filter()->unique()->toArray();

        $materis = Materi::whereIn('kelas', $kelasNames ?: [''])
            ->select('id', 'id_materi', 'kategori', 'nama_kitab', 'halaman_mulai', 'halaman_akhir', 'total_halaman')
            ->orderBy('kategori')
            ->orderBy('nama_kitab')
            ->get();

        return response()->json([
            'kelas' => $santri->kelas,
            'materis' => $materis
        ]);
    }

    /**
     * Get detail materi (AJAX)
     */
    public function getDetailMateri(Request $request)
    {
        $materi = Materi::where('id_materi', $request->id_materi)->first();
        
        if (!$materi) {
            return response()->json(['error' => 'Materi tidak ditemukan'], 404);
        }

        // Check existing capaian
        $existingCapaian = null;
        if ($request->filled('id_santri') && $request->filled('id_semester')) {
            $existingCapaian = Capaian::where('id_santri', $request->id_santri)
                ->where('id_materi', $request->id_materi)
                ->where('id_semester', $request->id_semester)
                ->first();
        }

        return response()->json([
            'materi' => $materi,
            'existing_capaian' => $existingCapaian
        ]);
    }

    /**
     * Store a newly created capaian (atau update jika sudah ada)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'id_materi' => 'required|exists:materi,id_materi',
            'id_semester' => 'required|exists:semester,id_semester',
            'halaman_selesai' => 'required|string',
            'catatan' => 'nullable|string',
            'tanggal_input' => 'required|date',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_materi.required' => 'Materi wajib dipilih.',
            'id_semester.required' => 'Semester wajib dipilih.',
            'halaman_selesai.required' => 'Halaman yang selesai wajib diisi.',
            'tanggal_input.required' => 'Tanggal input wajib diisi.',
        ]);

        // Check apakah capaian sudah ada (auto-created atau manual)
        $existing = Capaian::where('id_santri', $validated['id_santri'])
            ->where('id_materi', $validated['id_materi'])
            ->where('id_semester', $validated['id_semester'])
            ->first();

        if ($existing) {
            // Update existing capaian
            $existing->update([
                'halaman_selesai' => $validated['halaman_selesai'],
                'catatan' => $validated['catatan'],
                'tanggal_input' => $validated['tanggal_input'],
            ]);

            return redirect()->route('admin.capaian.show', $existing)
                ->with('success', 'Capaian berhasil diperbarui.');
        }

        // Create new capaian jika belum ada
        $capaian = Capaian::create($validated);

        return redirect()->route('admin.capaian.show', $capaian)
            ->with('success', 'Capaian berhasil ditambahkan.');
    }

    /**
     * Display the specified capaian
     */
    public function show(Capaian $capaian)
    {
        $capaian->load(['santri.kelasPrimary.kelas', 'materi', 'semester']);
        
        return view('admin.capaian.show', compact('capaian'));
    }

    /**
     * Show the form for editing the specified capaian
     */
    public function edit(Capaian $capaian)
    {
        $capaian->load(['santri.kelasPrimary.kelas', 'materi', 'semester']);
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.capaian.edit', compact('capaian', 'semesters'));
    }

    /**
     * Update the specified capaian
     */
    public function update(Request $request, Capaian $capaian)
    {
        $validated = $request->validate([
            'halaman_selesai' => 'required|string',
            'catatan' => 'nullable|string',
            'tanggal_input' => 'required|date',
        ], [
            'halaman_selesai.required' => 'Halaman yang selesai wajib diisi.',
            'tanggal_input.required' => 'Tanggal input wajib diisi.',
        ]);

        $capaian->update($validated);

        return redirect()->route('admin.capaian.show', $capaian)
            ->with('success', 'Capaian berhasil diperbarui.');
    }

    /**
     * Remove the specified capaian
     */
    public function destroy(Capaian $capaian)
    {
        $santriNama = $capaian->santri->nama_lengkap;
        $materiNama = $capaian->materi->nama_kitab;
        
        $capaian->delete();

        return redirect()->route('admin.capaian.index')
            ->with('success', "Capaian {$santriNama} untuk materi {$materiNama} berhasil dihapus.");
    }

    /**
     * Show riwayat capaian per santri
     */
    public function riwayatSantri($id_santri, Request $request)
    {
        $santri = Santri::where('id_santri', $id_santri)
            ->with('kelasPrimary.kelas')
            ->firstOrFail();
        
        $query = Capaian::with(['materi', 'semester'])
            ->bySantri($id_santri);

        // Filter semester
        if ($request->filled('id_semester')) {
            $query->bySemester($request->id_semester);
        }

        // Filter search (nama materi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('materi', function($q) use ($search) {
                $q->where('nama_kitab', 'like', "%{$search}%");
            });
        }

        $capaians = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(request()->query());

        // Statistik
        $totalCapaian = $capaians->total();
        $rataRataPersentase = Capaian::bySantri($id_santri)->avg('persentase') ?? 0;
        
        // Statistik per kategori
        $statistikKategori = Capaian::bySantri($id_santri)
            ->join('materi', 'capaian.id_materi', '=', 'materi.id_materi')
            ->select('materi.kategori', DB::raw('AVG(capaian.persentase) as rata_rata'))
            ->groupBy('materi.kategori')
            ->get()
            ->pluck('rata_rata', 'kategori')
            ->toArray();

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.capaian.riwayat-santri', compact('santri', 'capaians', 'totalCapaian', 'rataRataPersentase', 'statistikKategori', 'semesters'));
    }

    /**
     * Calculate persentase (AJAX untuk preview)
     */
    public function calculatePersentase(Request $request)
    {
        $halamanSelesai = $request->halaman_selesai;
        $idMateri = $request->id_materi;

        if (empty($halamanSelesai) || empty($idMateri)) {
            return response()->json(['persentase' => 0, 'jumlah' => 0]);
        }

        try {
            $persentase = Capaian::calculatePersentase($halamanSelesai, $idMateri);
            $pages = Capaian::parseHalamanSelesai($halamanSelesai);
            $jumlah = count($pages);

            return response()->json([
                'persentase' => number_format($persentase, 2),
                'jumlah' => $jumlah,
                'pages' => $pages
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Dashboard capaian dengan visualisasi lengkap
     */
    public function dashboard(Request $request)
    {
        // === FILTERS ===
        $kelas = $request->input('kelas');
        $idSemester = $request->input('id_semester');
        $semesterAktif = Semester::aktif()->first();
        $selectedSemester = $idSemester ?: ($semesterAktif ? $semesterAktif->id_semester : null);

        // === BASE DATA ===
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->orderBy('periode', 'desc')->get();
        $allSemestersOrdered = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
        $materis = Materi::orderBy('kategori')->orderBy('nama_kitab')->get();
        
        // Dynamic kelas list - HANYA kelas yang ada santri PRIMARY-nya
        $primaryKelasIds = SantriKelas::where('is_primary', true)
            ->distinct()
            ->pluck('id_kelas');
        
        $kelasModels = Kelas::active()
            ->whereIn('id', $primaryKelasIds)
            ->ordered()
            ->with('kelompok')
            ->get();
        
        $kelasList = $kelasModels->pluck('nama_kelas')->unique()->values()->toArray();

        $santrisAktif = Santri::where('status', 'Aktif')
            ->with(['kelasPrimary.kelas'])
            ->when($kelas, fn($q) => $q->primaryKelasByName($kelas))
            ->orderBy('nama_lengkap')->get();
        $santrisKhatam = Santri::where('status', 'Khatam')
            ->with(['kelasPrimary.kelas'])
            ->when($kelas, fn($q) => $q->primaryKelasByName($kelas))
            ->orderBy('nama_lengkap')->get();

        // === ALL CAPAIAN (eager loaded once, filter by PRIMARY kelas only) ===
        $allCapaian = Capaian::with(['santri.kelasPrimary.kelas', 'materi', 'semester'])
            ->when($kelas, fn($q) => $q->whereHas('santri', fn($sq) => $sq->primaryKelasByName($kelas)))
            ->get();

        $filteredCapaian = $selectedSemester
            ? $allCapaian->where('id_semester', $selectedSemester)
            : $allCapaian;

        // === 1. KPI SUMMARY ===
        $totalCapaian = $filteredCapaian->count();
        $totalSantriAktif = $santrisAktif->count();
        $rataRataProgress = $filteredCapaian->avg('persentase') ?? 0;
        $capaianSelesai = $filteredCapaian->where('persentase', '>=', 100)->count();

        $statistikKategori = [];
        foreach (['Al-Qur\'an', 'Hadist', 'Materi Tambahan'] as $kat) {
            $katCap = $filteredCapaian->filter(fn($c) => $c->materi && $c->materi->kategori === $kat);
            $statistikKategori[$kat] = [
                'count' => $katCap->count(),
                'avg' => round($katCap->avg('persentase') ?? 0, 2),
                'selesai' => $katCap->where('persentase', '>=', 100)->count(),
            ];
        }

        $distribusiProgress = [
            '0-25%' => $filteredCapaian->where('persentase', '>=', 0)->where('persentase', '<=', 25)->count(),
            '26-50%' => $filteredCapaian->where('persentase', '>', 25)->where('persentase', '<=', 50)->count(),
            '51-75%' => $filteredCapaian->where('persentase', '>', 50)->where('persentase', '<=', 75)->count(),
            '76-99%' => $filteredCapaian->where('persentase', '>', 75)->where('persentase', '<', 100)->count(),
            '100%' => $filteredCapaian->where('persentase', '>=', 100)->count(),
        ];

        // === 2. REKAP PER KELAS (Ranking + Khatam) ===
        $rekapKelas = [];
        foreach ($kelasList as $k) {
            $kelasCapaian = $filteredCapaian->filter(fn($c) => $c->santri && $c->santri->kelas === $k && $c->santri->status === 'Aktif');
            $santriIds = $kelasCapaian->pluck('id_santri')->unique();
            $ranking = [];

            foreach ($santriIds as $sid) {
                $sc = $kelasCapaian->where('id_santri', $sid);
                $santri = $sc->first()->santri;
                $kelasMateris = $materis->where('kelas', $k);
                $totalMateriKelas = $kelasMateris->count();
                $selesai = $sc->where('persentase', '>=', 100)->count();
                $avgProg = $sc->avg('persentase') ?? 0;
                $isFullKhatam = $totalMateriKelas > 0 && $selesai >= $totalMateriKelas;

                // Breakdown per kategori
                $alquran = $sc->filter(fn($c) => $c->materi->kategori == 'Al-Qur\'an')->avg('persentase') ?? 0;
                $hadist = $sc->filter(fn($c) => $c->materi->kategori == 'Hadist')->avg('persentase') ?? 0;
                $tambahan = $sc->filter(fn($c) => $c->materi->kategori == 'Materi Tambahan')->avg('persentase') ?? 0;

                $ranking[] = [
                    'santri' => $santri,
                    'avg_progress' => round($avgProg, 2),
                    'total_materi' => $sc->count(),
                    'selesai' => $selesai,
                    'total_materi_kelas' => $totalMateriKelas,
                    'is_full_khatam' => $isFullKhatam,
                    'alquran' => round($alquran, 1),
                    'hadist' => round($hadist, 1),
                    'tambahan' => round($tambahan, 1),
                ];
            }
            usort($ranking, fn($a, $b) => $b['avg_progress'] <=> $a['avg_progress']);

            $khatamSantris = Santri::primaryKelasByName($k)->where('status', 'Khatam')->get();

            // Summary stats per kelas
            $totalSantri = count($ranking);
            $avgProgress = $totalSantri > 0 ? collect($ranking)->avg('avg_progress') : 0;
            $totalSelesai = collect($ranking)->sum('selesai');
            $santriTuntas = collect($ranking)->where('avg_progress', '>=', 100)->count();

            $rekapKelas[$k] = [
                'ranking' => $ranking,
                'khatam' => $khatamSantris,
                'total_aktif' => Santri::primaryKelasByName($k)->where('status', 'Aktif')->count(),
                'summary' => [
                    'total_santri' => $totalSantri,
                    'avg_progress' => round($avgProgress, 1),
                    'total_selesai' => $totalSelesai,
                    'santri_tuntas' => $santriTuntas,
                ],
            ];
        }

        // === 3. SEMESTER COMPARISON (Line Chart data) ===
        $semesterLabels = $allSemestersOrdered->pluck('nama_semester')->toArray();
        $semesterComparison = [];
        foreach ($kelasList as $k) {
            $dataPoints = [];
            foreach ($allSemestersOrdered as $sem) {
                $semCap = $allCapaian->where('id_semester', $sem->id_semester)
                    ->filter(fn($c) => $c->santri && $c->santri->kelas === $k);
                $dataPoints[] = round($semCap->avg('persentase') ?? 0, 2);
            }
            $semesterComparison[$k] = $dataPoints;
        }

        // === 4. SEMESTER-OVER-SEMESTER GROWTH ===
        $sosGrowth = [];
        $santriIdsForGrowth = $filteredCapaian->pluck('id_santri')->unique()->take(25);
        foreach ($santriIdsForGrowth as $sid) {
            $santri = $santrisAktif->where('id_santri', $sid)->first();
            if (!$santri) continue;

            $semProgress = [];
            foreach ($allSemestersOrdered as $sem) {
                $semCap = $allCapaian->where('id_santri', $sid)->where('id_semester', $sem->id_semester);
                $semProgress[] = round($semCap->avg('persentase') ?? 0, 2);
            }

            $growth = [];
            for ($i = 0; $i < count($semProgress); $i++) {
                $growth[] = $i > 0 ? round($semProgress[$i] - $semProgress[$i - 1], 2) : 0;
            }

            $sosGrowth[] = [
                'nama' => $santri->nama_lengkap,
                'id_santri' => $sid,
                'kelas' => $santri->kelas,
                'progress' => $semProgress,
                'growth' => $growth,
                'current' => end($semProgress) ?: 0,
            ];
        }
        usort($sosGrowth, fn($a, $b) => $b['current'] <=> $a['current']);

        // === 5. MATERI COMPLETION RATE PER SEMESTER ===
        $materiCompletionRate = [];
        $filteredMateris = $kelas ? $materis->where('kelas', $kelas) : $materis;
        foreach ($filteredMateris as $materi) {
            $rates = [];
            foreach ($allSemestersOrdered as $sem) {
                $semMatCap = $allCapaian->where('id_materi', $materi->id_materi)
                    ->where('id_semester', $sem->id_semester);
                $total = $semMatCap->count();
                $selesai = $semMatCap->where('persentase', '>=', 100)->count();
                $rates[$sem->id_semester] = $total > 0 ? round(($selesai / $total) * 100, 1) : null;
            }
            $materiCompletionRate[] = [
                'materi' => $materi,
                'rates' => $rates,
            ];
        }

        // === 7. BOTTLENECK ANALYSIS ===
        $bottleneckMateri = [];
        foreach ($filteredMateris as $materi) {
            $matCap = $filteredCapaian->where('id_materi', $materi->id_materi);
            if ($matCap->isEmpty()) continue;
            $avgProg = $matCap->avg('persentase') ?? 0;
            $totalS = $matCap->count();
            $stuckS = $matCap->where('persentase', '<', 50)->count();
            $stuckPct = $totalS > 0 ? round(($stuckS / $totalS) * 100, 1) : 0;

            $bottleneckMateri[] = [
                'materi' => $materi,
                'avg_progress' => round($avgProg, 2),
                'total_santri' => $totalS,
                'stuck_santri' => $stuckS,
                'stuck_percentage' => $stuckPct,
            ];
        }
        usort($bottleneckMateri, fn($a, $b) => $b['stuck_percentage'] <=> $a['stuck_percentage']);
        $bottleneckMateri = array_slice($bottleneckMateri, 0, 10);

        // === 8. PROJECTED GRADUATION TIMELINE ===
        $projectedGraduation = [];
        foreach ($santrisAktif->take(25) as $santri) {
            $santriCap = $allCapaian->where('id_santri', $santri->id_santri);
            if ($santriCap->isEmpty()) continue;

            $progressPerSem = [];
            foreach ($allSemestersOrdered as $sem) {
                $semCap = $santriCap->where('id_semester', $sem->id_semester);
                if ($semCap->isNotEmpty()) {
                    $progressPerSem[] = ['sem' => $sem->nama_semester, 'avg' => round($semCap->avg('persentase'), 2)];
                }
            }
            $currentProgress = round($santriCap->avg('persentase') ?? 0, 2);

            // Calculate growth rate
            $growthRate = 0;
            if (count($progressPerSem) >= 2) {
                $diffs = [];
                for ($i = 1; $i < count($progressPerSem); $i++) {
                    $diffs[] = $progressPerSem[$i]['avg'] - $progressPerSem[$i - 1]['avg'];
                }
                $growthRate = count($diffs) > 0 ? round(array_sum($diffs) / count($diffs), 2) : 0;
            } elseif (count($progressPerSem) === 1) {
                $growthRate = $progressPerSem[0]['avg'];
            }

            $remaining = 100 - $currentProgress;
            $semestersToGrad = ($growthRate > 0 && $currentProgress < 100) ? ceil($remaining / $growthRate) : ($currentProgress >= 100 ? 0 : null);

            $projectedGraduation[] = [
                'santri' => $santri,
                'current_progress' => $currentProgress,
                'growth_rate' => $growthRate,
                'semesters_to_grad' => $semestersToGrad,
                'history' => $progressPerSem,
            ];
        }
        usort($projectedGraduation, fn($a, $b) => $b['current_progress'] <=> $a['current_progress']);

        // === 9. SEMESTER SUMMARY REPORT ===
        $semesterSummary = null;
        if ($selectedSemester) {
            $selectedSem = $semesters->where('id_semester', $selectedSemester)->first();
            $semCap = $allCapaian->where('id_semester', $selectedSemester);

            $currentIdx = $allSemestersOrdered->search(fn($s) => $s->id_semester === $selectedSemester);
            $prevSemester = $currentIdx > 0 ? $allSemestersOrdered[$currentIdx - 1] : null;
            $prevSemCap = $prevSemester ? $allCapaian->where('id_semester', $prevSemester->id_semester) : collect();

            $avgProgressSem = $semCap->avg('persentase') ?? 0;
            $avgProgressPrev = $prevSemCap->isNotEmpty() ? ($prevSemCap->avg('persentase') ?? 0) : 0;
            $kenaikan = $avgProgressSem - $avgProgressPrev;

            // Santri fully complete all materi
            $santriFullKhatam = 0;
            $santriIds = $semCap->pluck('id_santri')->unique();
            foreach ($santriIds as $sid) {
                $sCap = $semCap->where('id_santri', $sid);
                if ($sCap->isNotEmpty() && $sCap->every(fn($c) => $c->persentase >= 100)) {
                    $santriFullKhatam++;
                }
            }

            // Santri remedial (avg < 30%)
            $santriRemedialCount = 0;
            $santriRemedialList = [];
            foreach ($santriIds as $sid) {
                $sCap = $semCap->where('id_santri', $sid);
                if (($sCap->avg('persentase') ?? 0) < 30) {
                    $santriRemedialCount++;
                    $s = $santrisAktif->where('id_santri', $sid)->first();
                    if ($s) $santriRemedialList[] = $s;
                }
            }

            // Materi paling banyak dikhatamkan
            $materiKhatamList = $semCap->where('persentase', '>=', 100)
                ->groupBy('id_materi')
                ->map(fn($g) => ['count' => $g->count(), 'materi' => $g->first()->materi])
                ->sortByDesc('count')->take(5)->values();

            // Materi paling sedikit progress
            $materiMinList = $semCap->groupBy('id_materi')
                ->map(fn($g) => ['avg' => round($g->avg('persentase'), 2), 'materi' => $g->first()->materi])
                ->sortBy('avg')->take(5)->values();

            $semesterSummary = [
                'semester' => $selectedSem,
                'prev_semester' => $prevSemester,
                'total_santri' => $santriIds->count(),
                'avg_progress' => round($avgProgressSem, 2),
                'avg_progress_prev' => round($avgProgressPrev, 2),
                'kenaikan' => round($kenaikan, 2),
                'santri_khatam' => $santriFullKhatam,
                'santri_remedial_count' => $santriRemedialCount,
                'santri_remedial' => $santriRemedialList,
                'materi_khatam' => $materiKhatamList,
                'materi_min' => $materiMinList,
            ];
        }

        return view('admin.capaian.dashboard', compact(
            'semesters', 'allSemestersOrdered', 'selectedSemester', 'semesterAktif',
            'kelas', 'kelasList', 'kelasModels', 'santrisAktif', 'santrisKhatam', 'materis',
            'totalCapaian', 'totalSantriAktif', 'rataRataProgress', 'capaianSelesai',
            'statistikKategori', 'distribusiProgress',
            'rekapKelas',
            'semesterLabels', 'semesterComparison',
            'sosGrowth',
            'materiCompletionRate',
            'bottleneckMateri',
            'projectedGraduation',
            'semesterSummary'
        ));
    }

    /**
     * Tandai santri sebagai Khatam
     */
    public function tandaiKhatam($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        $santri->update(['status' => 'Khatam']);
        return redirect()->back()->with('success', "Santri {$santri->nama_lengkap} berhasil ditandai sebagai Khatam.");
    }

    /**
     * Batalkan status Khatam
     */
    public function batalKhatam($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        $santri->update(['status' => 'Aktif']);
        return redirect()->back()->with('success', "Status Khatam santri {$santri->nama_lengkap} berhasil dibatalkan.");
    }

    /**
     * Export Rapor Per Santri Per Semester
     */
    public function exportRapor($id_santri, $id_semester)
    {
        $santri = Santri::where('id_santri', $id_santri)
            ->with('kelasPrimary.kelas')
            ->firstOrFail();
        $semester = Semester::where('id_semester', $id_semester)->firstOrFail();

        $capaians = Capaian::where('id_santri', $id_santri)
            ->where('id_semester', $id_semester)
            ->with('materi')
            ->orderBy('created_at')
            ->get();

        // Previous semester for comparison
        $allSem = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
        $curIdx = $allSem->search(fn($s) => $s->id_semester === $id_semester);
        $prevSemester = $curIdx > 0 ? $allSem[$curIdx - 1] : null;
        $prevCapaians = $prevSemester
            ? Capaian::where('id_santri', $id_santri)->where('id_semester', $prevSemester->id_semester)->with('materi')->get()
            : collect();

        // Stats
        $avgProgress = $capaians->avg('persentase') ?? 0;
        $avgPrev = $prevCapaians->avg('persentase') ?? 0;
        $selesai = $capaians->where('persentase', '>=', 100)->count();
        $totalMateri = $capaians->count();

        // Per kategori
        $perKategori = [];
        foreach (['Al-Qur\'an', 'Hadist', 'Materi Tambahan'] as $kat) {
            $katCap = $capaians->filter(fn($c) => $c->materi && $c->materi->kategori === $kat);
            $katPrev = $prevCapaians->filter(fn($c) => $c->materi && $c->materi->kategori === $kat);
            $perKategori[$kat] = [
                'avg' => round($katCap->avg('persentase') ?? 0, 2),
                'prev' => round($katPrev->avg('persentase') ?? 0, 2),
                'count' => $katCap->count(),
                'selesai' => $katCap->where('persentase', '>=', 100)->count(),
            ];
        }

        return view('admin.capaian.export-rapor', compact(
            'santri', 'semester', 'capaians', 'prevSemester', 'prevCapaians',
            'avgProgress', 'avgPrev', 'selesai', 'totalMateri', 'perKategori'
        ));
    }

    /**
     * Detail capaian per materi (semua santri)
     */
    public function detailMateri($id_materi, Request $request)
    {
        $materi = Materi::where('id_materi', $id_materi)->firstOrFail();
        
        $idSemester = $request->input('id_semester');
        $semesterAktif = Semester::aktif()->first();
        $selectedSemester = $idSemester ?: ($semesterAktif ? $semesterAktif->id_semester : null);

        // Get all capaian untuk materi ini
        $capaians = Capaian::where('id_materi', $id_materi)
            ->when($selectedSemester, function($q) use ($selectedSemester) {
                return $q->where('id_semester', $selectedSemester);
            })
            ->with(['santri.kelasPrimary.kelas', 'semester'])
            ->orderBy('persentase', 'desc')
            ->get();

        // Statistik
        $totalSantri = $capaians->count();
        $rataRataPersentase = $capaians->avg('persentase') ?? 0;
        $santriSelesai = $capaians->where('persentase', '>=', 100)->count();
        $santriMulai = $capaians->where('persentase', '>', 0)->where('persentase', '<', 100)->count();

        // Distribusi persentase
        $distribusi = [
            '0-25%' => $capaians->whereBetween('persentase', [0, 25])->count(),
            '26-50%' => $capaians->whereBetween('persentase', [26, 50])->count(),
            '51-75%' => $capaians->whereBetween('persentase', [51, 75])->count(),
            '76-99%' => $capaians->whereBetween('persentase', [76, 99])->count(),
            '100%' => $capaians->where('persentase', '>=', 100)->count(),
        ];

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.capaian.detail-materi', compact(
            'materi',
            'capaians',
            'totalSantri',
            'rataRataPersentase',
            'santriSelesai',
            'santriMulai',
            'distribusi',
            'semesters',
            'selectedSemester'
        ));
    }

    /**
     * API untuk data grafik (AJAX)
     */
    public function apiGrafikData(Request $request)
    {
        $type = $request->input('type', 'kategori');
        $idSemester = $request->input('id_semester');
        $kelas = $request->input('kelas');

        $query = Capaian::with(['santri', 'materi']);

        if ($idSemester) {
            $query->bySemester($idSemester);
        }

        if ($kelas) {
            $query->whereHas('santri', function($q) use ($kelas) {
                $q->kelasByName($kelas);
            });
        }

        $data = [];

        switch ($type) {
            case 'kategori':
                $data = [
                    'labels' => ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
                    'datasets' => [[
                        'label' => 'Rata-rata Progress (%)',
                        'data' => [
                            $query->clone()->byKategori('Al-Qur\'an')->avg('persentase') ?? 0,
                            $query->clone()->byKategori('Hadist')->avg('persentase') ?? 0,
                            $query->clone()->byKategori('Materi Tambahan')->avg('persentase') ?? 0,
                        ],
                        'backgroundColor' => [
                            'rgba(111, 186, 157, 0.8)',
                            'rgba(129, 198, 232, 0.8)',
                            'rgba(255, 213, 107, 0.8)',
                        ],
                    ]]
                ];
                break;

            case 'distribusi':
                $capaians = $query->get();
                $data = [
                    'labels' => ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
                    'datasets' => [[
                        'label' => 'Jumlah Santri',
                        'data' => [
                            $capaians->whereBetween('persentase', [0, 25])->count(),
                            $capaians->whereBetween('persentase', [26, 50])->count(),
                            $capaians->whereBetween('persentase', [51, 75])->count(),
                            $capaians->whereBetween('persentase', [76, 99])->count(),
                            $capaians->where('persentase', '>=', 100)->count(),
                        ],
                        'backgroundColor' => [
                            'rgba(255, 139, 148, 0.8)',
                            'rgba(255, 171, 145, 0.8)',
                            'rgba(255, 213, 107, 0.8)',
                            'rgba(129, 198, 232, 0.8)',
                            'rgba(111, 186, 157, 0.8)',
                        ],
                    ]]
                ];
                break;

            case 'trend':
                // Get data per semester
                $semesters = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
                $labels = [];
                $dataPoints = [];

                foreach ($semesters as $semester) {
                    $labels[] = $semester->nama_semester;
                    $avg = Capaian::where('id_semester', $semester->id_semester)
                        ->when($kelas, function($q) use ($kelas) {
                            return $q->whereHas('santri', function($query) use ($kelas) {
                                $query->kelasByName($kelas);
                            });
                        })
                        ->avg('persentase') ?? 0;
                    $dataPoints[] = round($avg, 2);
                }

                $data = [
                    'labels' => $labels,
                    'datasets' => [[
                        'label' => 'Rata-rata Progress (%)',
                        'data' => $dataPoints,
                        'borderColor' => 'rgba(111, 186, 157, 1)',
                        'backgroundColor' => 'rgba(111, 186, 157, 0.2)',
                        'tension' => 0.4,
                    ]]
                ];
                break;
        }

        return response()->json($data);
    }
}