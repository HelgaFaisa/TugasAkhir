<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Capaian;
use App\Models\Santri;
use App\Models\Materi;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\SantriKelas;
use App\Services\CapaianAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapaianController extends Controller
{
    // =====================================================================
    //  INDEX — daftar santri + total progress
    // =====================================================================
    public function index(Request $request)
    {
        $semesters     = Semester::orderBy('tahun_ajaran', 'desc')->get();
        $semesterAktif = Semester::aktif()->first();

        $selectedKelas    = $request->input('id_kelas');
        $selectedSemester = $request->input('id_semester', $semesterAktif?->id_semester);
        $search           = $request->input('search');

        $kelasList = Kelas::active()->ordered()->with('kelompok')->get();

        $query = Santri::where('status', 'Aktif')
            ->with(['kelasPrimary.kelas.kelompok']);

        if ($selectedKelas) {
            $query->kelas($selectedKelas);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $santris = $query->orderBy('nama_lengkap')->get();

        $santriData = $santris->map(function ($santri) use ($selectedSemester) {
            $capaians = Capaian::where('id_santri', $santri->id_santri)
                ->when($selectedSemester, fn($q) => $q->where('id_semester', $selectedSemester))
                ->get();

            $capaiansBerisi = $capaians->where('persentase', '>', 0);
            $totalProgress  = $capaiansBerisi->isEmpty() ? 0 : $capaiansBerisi->avg('persentase');

            return [
                'santri'         => $santri,
                'total_progress' => round($totalProgress, 2),
                'total_materi'   => $capaiansBerisi->count(),
                'capaians'       => $capaians,
            ];
        })->sortBy('total_progress')->values();

        return view('admin.capaian.index', compact(
            'santriData', 'semesters', 'kelasList',
            'selectedKelas', 'selectedSemester', 'search'
        ));
    }

    // =====================================================================
    //  CREATE / STORE
    // =====================================================================
    public function create(Request $request)
    {
        $santris       = Santri::aktif()->select('id', 'id_santri', 'nis', 'nama_lengkap')
                            ->with(['kelasPrimary.kelas'])->orderBy('nama_lengkap')->get();
        $semesterAktif = Semester::aktif()->first();
        $semesters     = Semester::orderBy('tahun_ajaran', 'desc')->get();

        $selectedSantri = null;
        $materiOptions  = [];

        if ($request->filled('id_santri')) {
            $selectedSantri = Santri::where('id_santri', $request->id_santri)
                ->with(['kelasSantri.kelas'])->first();
            if ($selectedSantri) {
                $kelasNames    = $selectedSantri->kelasSantri
                    ->map(fn($sk) => $sk->kelas?->nama_kelas)->filter()->unique()->toArray();
                $materiOptions = Materi::whereIn('kelas', $kelasNames ?: [''])
                    ->orderBy('kategori')->orderBy('nama_kitab')->get();
            }
        }

        return view('admin.capaian.create', compact(
            'santris', 'semesters', 'semesterAktif', 'selectedSantri', 'materiOptions'
        ));
    }

    public function getMateriByKelas(Request $request)
    {
        $santri = Santri::where('id_santri', $request->id_santri)
            ->with(['kelasSantri.kelas'])->first();

        if (!$santri) return response()->json(['error' => 'Santri tidak ditemukan'], 404);

        $kelasNames = $santri->kelasSantri
            ->map(fn($sk) => $sk->kelas?->nama_kelas)->filter()->unique()->toArray();

        $materis = Materi::whereIn('kelas', $kelasNames ?: [''])
            ->select('id', 'id_materi', 'kategori', 'nama_kitab', 'halaman_mulai', 'halaman_akhir', 'total_halaman')
            ->orderBy('kategori')->orderBy('nama_kitab')->get();

        return response()->json(['kelas' => $santri->kelas, 'materis' => $materis]);
    }

    public function getDetailMateri(Request $request)
    {
        $materi = Materi::where('id_materi', $request->id_materi)->first();
        if (!$materi) return response()->json(['error' => 'Materi tidak ditemukan'], 404);

        $existingCapaian = null;
        if ($request->filled('id_santri') && $request->filled('id_semester')) {
            $existingCapaian = Capaian::where('id_santri', $request->id_santri)
                ->where('id_materi', $request->id_materi)
                ->where('id_semester', $request->id_semester)->first();
        }

        return response()->json(['materi' => $materi, 'existing_capaian' => $existingCapaian]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri'       => 'required|exists:santris,id_santri',
            'id_materi'       => 'required|exists:materi,id_materi',
            'id_semester'     => 'required|exists:semester,id_semester',
            'halaman_selesai' => 'required|string',
            'catatan'         => 'nullable|string',
            'tanggal_input'   => 'required|date',
        ]);

        $existing = Capaian::where('id_santri', $validated['id_santri'])
            ->where('id_materi', $validated['id_materi'])
            ->where('id_semester', $validated['id_semester'])->first();

        if ($existing) {
            $existing->update([
                'halaman_selesai' => $validated['halaman_selesai'],
                'catatan'         => $validated['catatan'],
                'tanggal_input'   => $validated['tanggal_input'],
            ]);
            return redirect()->route('admin.capaian.show', $existing)
                ->with('success', 'Capaian berhasil diperbarui.');
        }

        $capaian = Capaian::create($validated);
        return redirect()->route('admin.capaian.show', $capaian)
            ->with('success', 'Capaian berhasil ditambahkan.');
    }

    // =====================================================================
    //  SHOW / EDIT / UPDATE / DESTROY
    // =====================================================================
    public function show(Capaian $capaian)
    {
        $capaian->load(['santri.kelasPrimary.kelas', 'materi', 'semester']);
        return view('admin.capaian.show', compact('capaian'));
    }

    public function edit(Capaian $capaian)
    {
        $capaian->load(['santri.kelasPrimary.kelas', 'materi', 'semester']);
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();
        return view('admin.capaian.edit', compact('capaian', 'semesters'));
    }

    public function update(Request $request, Capaian $capaian)
    {
        $validated = $request->validate([
            'halaman_selesai' => 'required|string',
            'catatan'         => 'nullable|string',
            'tanggal_input'   => 'required|date',
        ]);

        $capaian->update($validated);
        return redirect()->route('admin.capaian.show', $capaian)
            ->with('success', 'Capaian berhasil diperbarui.');
    }

    public function destroy(Capaian $capaian)
    {
        $santriNama = $capaian->santri->nama_lengkap;
        $materiNama = $capaian->materi->nama_kitab;
        $capaian->delete();
        return redirect()->route('admin.capaian.index')
            ->with('success', "Capaian {$santriNama} untuk materi {$materiNama} berhasil dihapus.");
    }

    // =====================================================================
    //  RIWAYAT SANTRI
    // =====================================================================
    public function riwayatSantri($id_santri, Request $request)
    {
        $santri = Santri::where('id_santri', $id_santri)
            ->with('kelasPrimary.kelas')->firstOrFail();

        $query = Capaian::with(['materi', 'semester'])->bySantri($id_santri);

        if ($request->filled('id_semester')) $query->bySemester($request->id_semester);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('materi', fn($q) => $q->where('nama_kitab', 'like', "%{$search}%"));
        }

        $capaians           = $query->orderBy('created_at', 'desc')->paginate(15)->appends(request()->query());
        $totalCapaian       = $capaians->total();
        $rataRataPersentase = Capaian::bySantri($id_santri)->avg('persentase') ?? 0;

        $statistikKategori = Capaian::bySantri($id_santri)
            ->join('materi', 'capaian.id_materi', '=', 'materi.id_materi')
            ->select('materi.kategori', DB::raw('AVG(capaian.persentase) as rata_rata'))
            ->groupBy('materi.kategori')->get()
            ->pluck('rata_rata', 'kategori')->toArray();

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.capaian.riwayat-santri', compact(
            'santri', 'capaians', 'totalCapaian', 'rataRataPersentase', 'statistikKategori', 'semesters'
        ));
    }

    // =====================================================================
    //  CALCULATE PERSENTASE (AJAX)
    // =====================================================================
    public function calculatePersentase(Request $request)
    {
        if (empty($request->halaman_selesai) || empty($request->id_materi)) {
            return response()->json(['persentase' => 0, 'jumlah' => 0]);
        }
        try {
            $persentase = Capaian::calculatePersentase($request->halaman_selesai, $request->id_materi);
            $pages      = Capaian::parseHalamanSelesai($request->halaman_selesai);
            return response()->json([
                'persentase' => number_format($persentase, 2),
                'jumlah'     => count($pages),
                'pages'      => $pages,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // =====================================================================
    //  DASHBOARD
    // =====================================================================
    public function dashboard(Request $request)
    {
        // --- Filters ---
        $kelas            = $request->input('kelas');
        $idSemester       = $request->input('id_semester');
        $filterSantri     = $request->input('filter_santri', 'all');
        $semesterAktif    = Semester::aktif()->first();
        $selectedSemester = $idSemester ?: ($semesterAktif?->id_semester);

        $semesters           = Semester::orderBy('tahun_ajaran', 'desc')->orderBy('periode', 'desc')->get();
        $allSemestersOrdered = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
        $materis             = Materi::orderBy('kategori')->orderBy('nama_kitab')->get();

        // Kelas yang punya santri primary
        $primaryKelasIds = SantriKelas::where('is_primary', true)->distinct()->pluck('id_kelas');
        $kelasModels     = Kelas::active()->whereIn('id', $primaryKelasIds)->ordered()->with('kelompok')->get();
        $kelasList       = $kelasModels->pluck('nama_kelas')->unique()->values()->toArray();

        $santrisAktif = Santri::where('status', 'Aktif')
            ->with(['kelasPrimary.kelas'])
            ->when($kelas, fn($q) => $q->primaryKelasByName($kelas))
            ->orderBy('nama_lengkap')->get();

        $santrisKhatam = Santri::where('status', 'Khatam')
            ->with(['kelasPrimary.kelas'])
            ->when($kelas, fn($q) => $q->primaryKelasByName($kelas))
            ->orderBy('nama_lengkap')->get();

        // Load semua capaian sekali saja
        $allCapaian = Capaian::with(['santri.kelasPrimary.kelas', 'materi', 'semester'])
            ->when($kelas, fn($q) => $q->whereHas('santri', fn($sq) => $sq->primaryKelasByName($kelas)))
            ->get();

        $filteredCapaian = $selectedSemester
            ? $allCapaian->where('id_semester', $selectedSemester)
            : $allCapaian;

        // --- KPI ---
        $totalSantriAktif = $santrisAktif->count();
        $rataRataProgress = round($filteredCapaian->avg('persentase') ?? 0, 1);

        $statistikKategori = [];
        foreach (["Al-Qur'an", 'Hadist', 'Materi Tambahan'] as $kat) {
            $katCap = $filteredCapaian->filter(fn($c) => $c->materi && $c->materi->kategori === $kat);
            $statistikKategori[$kat] = [
                'count'   => $katCap->count(),
                'avg'     => round($katCap->avg('persentase') ?? 0, 2),
                'selesai' => $katCap->where('persentase', '>=', 100)->count(),
            ];
        }

        $distribusiProgress = [
            '0-25%'  => $filteredCapaian->where('persentase', '>=', 0)->where('persentase', '<=', 25)->count(),
            '26-50%' => $filteredCapaian->where('persentase', '>', 25)->where('persentase', '<=', 50)->count(),
            '51-75%' => $filteredCapaian->where('persentase', '>', 50)->where('persentase', '<=', 75)->count(),
            '76-99%' => $filteredCapaian->where('persentase', '>', 75)->where('persentase', '<', 100)->count(),
            '100%'   => $filteredCapaian->where('persentase', '>=', 100)->count(),
        ];

        // --- Rekap per kelas (Ranking tab) ---
        $rekapKelas = [];
        foreach ($kelasList as $k) {
            $kelasCapaian = $filteredCapaian->filter(
                fn($c) => $c->santri && $c->santri->kelas === $k && $c->santri->status === 'Aktif'
            );
            $santriIds = $kelasCapaian->pluck('id_santri')->unique();
            $ranking   = [];

            foreach ($santriIds as $sid) {
                $sc               = $kelasCapaian->where('id_santri', $sid);
                $santri           = $sc->first()->santri;
                $kelasMateris     = $materis->where('kelas', $k);
                $totalMateriKelas = $kelasMateris->count();
                $selesai          = $sc->where('persentase', '>=', 100)->count();
                $avgProg          = round($sc->avg('persentase') ?? 0, 2);
                $isFullKhatam     = $totalMateriKelas > 0 && $selesai >= $totalMateriKelas;

                $ranking[] = [
                    'santri'             => $santri,
                    'avg_progress'       => $avgProg,
                    'selesai'            => $selesai,
                    'total_materi_kelas' => $totalMateriKelas,
                    'is_full_khatam'     => $isFullKhatam,
                    'alquran'            => round($sc->filter(fn($c) => $c->materi->kategori == "Al-Qur'an")->avg('persentase') ?? 0, 1),
                    'hadist'             => round($sc->filter(fn($c) => $c->materi->kategori == 'Hadist')->avg('persentase') ?? 0, 1),
                    'tambahan'           => round($sc->filter(fn($c) => $c->materi->kategori == 'Materi Tambahan')->avg('persentase') ?? 0, 1),
                ];
            }
            usort($ranking, fn($a, $b) => $b['avg_progress'] <=> $a['avg_progress']);

            $khatamSantris = Santri::primaryKelasByName($k)->where('status', 'Khatam')->get();
            $totalSantri   = count($ranking);

            $rekapKelas[$k] = [
                'ranking'     => $ranking,
                'khatam'      => $khatamSantris,
                'total_aktif' => Santri::primaryKelasByName($k)->where('status', 'Aktif')->count(),
                'summary'     => [
                    'total_santri'  => $totalSantri,
                    'avg_progress'  => $totalSantri > 0 ? round(collect($ranking)->avg('avg_progress'), 1) : 0,
                    'total_selesai' => collect($ranking)->sum('selesai'),
                    'santri_tuntas' => collect($ranking)->where('avg_progress', '>=', 100)->count(),
                ],
            ];
        }

        // --- Semester comparison (Line chart) ---
        $semesterLabels     = $allSemestersOrdered->pluck('nama_semester')->toArray();
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

        // --- Materi completion rate ---
        $filteredMateris      = $kelas ? $materis->where('kelas', $kelas) : $materis;
        $materiCompletionRate = [];
        foreach ($filteredMateris as $materi) {
            $rates = [];
            foreach ($allSemestersOrdered as $sem) {
                $semMatCap = $allCapaian->where('id_materi', $materi->id_materi)
                    ->where('id_semester', $sem->id_semester);
                $total   = $semMatCap->count();
                $selesai = $semMatCap->where('persentase', '>=', 100)->count();
                $rates[$sem->id_semester] = $total > 0 ? round(($selesai / $total) * 100, 1) : null;
            }
            $materiCompletionRate[] = ['materi' => $materi, 'rates' => $rates];
        }

        // --- Bottleneck ---
        $bottleneckMateri = [];
        foreach ($filteredMateris as $materi) {
            $matCap = $filteredCapaian->where('id_materi', $materi->id_materi);
            if ($matCap->isEmpty()) continue;
            $totalS   = $matCap->count();
            $stuckS   = $matCap->where('persentase', '<', 50)->count();
            $stuckPct = $totalS > 0 ? round(($stuckS / $totalS) * 100, 1) : 0;
            $bottleneckMateri[] = [
                'materi'           => $materi,
                'avg_progress'     => round($matCap->avg('persentase') ?? 0, 2),
                'total_santri'     => $totalS,
                'stuck_santri'     => $stuckS,
                'stuck_percentage' => $stuckPct,
            ];
        }
        usort($bottleneckMateri, fn($a, $b) => $b['stuck_percentage'] <=> $a['stuck_percentage']);
        $bottleneckMateri = array_slice($bottleneckMateri, 0, 10);

        // --- Projected Graduation (per kelas, tab Progress Santri) ---
        $projectedByKelas = [];
        foreach ($santrisAktif as $santri) {
            $santriCap = $allCapaian->where('id_santri', $santri->id_santri);
            if ($santriCap->isEmpty()) continue;

            $progressPerSem = [];
            foreach ($allSemestersOrdered as $sem) {
                $semCap = $santriCap->where('id_semester', $sem->id_semester);
                if ($semCap->isNotEmpty()) {
                    $progressPerSem[] = [
                        'sem' => $sem->nama_semester,
                        'avg' => round($semCap->avg('persentase'), 2),
                    ];
                }
            }

            $currentProgress = round($santriCap->avg('persentase') ?? 0, 2);
            $growthRate      = 0;

            if (count($progressPerSem) >= 2) {
                $diffs = [];
                for ($i = 1; $i < count($progressPerSem); $i++) {
                    $diffs[] = $progressPerSem[$i]['avg'] - $progressPerSem[$i - 1]['avg'];
                }
                $growthRate = count($diffs) > 0 ? round(array_sum($diffs) / count($diffs), 2) : 0;
            } elseif (count($progressPerSem) === 1) {
                $growthRate = $progressPerSem[0]['avg'];
            }

            $remaining       = 100 - $currentProgress;
            $semestersToGrad = ($growthRate > 0 && $currentProgress < 100)
                ? ceil($remaining / $growthRate)
                : ($currentProgress >= 100 ? 0 : null);

            $item = [
                'santri'            => $santri,
                'current_progress'  => $currentProgress,
                'growth_rate'       => $growthRate,
                'semesters_to_grad' => $semestersToGrad,
                'history'           => $progressPerSem,
            ];

            $kelasKey                      = $santri->kelas ?? 'Tanpa Kelas';
            $projectedByKelas[$kelasKey][] = $item;
        }
        foreach ($projectedByKelas as &$kItems) {
            usort($kItems, fn($a, $b) => $b['current_progress'] <=> $a['current_progress']);
        }
        unset($kItems);

        // --- Semester Summary Report ---
        $semesterSummary = null;
        if ($selectedSemester) {
            $selectedSem     = $semesters->where('id_semester', $selectedSemester)->first();
            $semCap          = $allCapaian->where('id_semester', $selectedSemester);
            $currentIdx      = $allSemestersOrdered->search(fn($s) => $s->id_semester === $selectedSemester);
            $prevSemester    = $currentIdx > 0 ? $allSemestersOrdered[$currentIdx - 1] : null;
            $prevSemCap      = $prevSemester
                ? $allCapaian->where('id_semester', $prevSemester->id_semester)
                : collect();
            $avgProgressSem  = $semCap->avg('persentase') ?? 0;
            $avgProgressPrev = $prevSemCap->isNotEmpty() ? ($prevSemCap->avg('persentase') ?? 0) : 0;

            $santriIds           = $semCap->pluck('id_santri')->unique();
            $santriFullKhatam    = 0;
            $santriRemedialCount = 0;
            $santriRemedialList  = [];
            foreach ($santriIds as $sid) {
                $sCap = $semCap->where('id_santri', $sid);
                if ($sCap->every(fn($c) => $c->persentase >= 100)) $santriFullKhatam++;
                if (($sCap->avg('persentase') ?? 0) < 30) {
                    $santriRemedialCount++;
                    $s = $santrisAktif->where('id_santri', $sid)->first();
                    if ($s) $santriRemedialList[] = $s;
                }
            }

            $materiKhatamList = $semCap->where('persentase', '>=', 100)
                ->groupBy('id_materi')
                ->map(fn($g) => ['count' => $g->count(), 'materi' => $g->first()->materi])
                ->sortByDesc('count')->take(5)->values();

            $materiMinList = $semCap->groupBy('id_materi')
                ->map(fn($g) => ['avg' => round($g->avg('persentase'), 2), 'materi' => $g->first()->materi])
                ->sortBy('avg')->take(5)->values();

            $semesterSummary = [
                'semester'              => $selectedSem,
                'prev_semester'         => $prevSemester,
                'total_santri'          => $santriIds->count(),
                'avg_progress'          => round($avgProgressSem, 2),
                'avg_progress_prev'     => round($avgProgressPrev, 2),
                'kenaikan'              => round($avgProgressSem - $avgProgressPrev, 2),
                'santri_khatam'         => $santriFullKhatam,
                'santri_remedial_count' => $santriRemedialCount,
                'santri_remedial'       => $santriRemedialList,
                'materi_khatam'         => $materiKhatamList,
                'materi_min'            => $materiMinList,
            ];
        }

        // --- Santri Ringkasan (overview tab) ---
        $santriProgressList = [];
        foreach ($santrisAktif as $santri) {
            $sc  = $filteredCapaian->where('id_santri', $santri->id_santri);
            $avg = $sc->isNotEmpty() ? round($sc->avg('persentase'), 1) : 0;
            $santriProgressList[] = ['santri' => $santri, 'avg' => $avg];
        }
        usort($santriProgressList, fn($a, $b) => $b['avg'] <=> $a['avg']);

        $DISPLAY_LIMIT   = 8;
        $santriRingkasan = match ($filterSantri) {
            'top'       => array_slice($santriProgressList, 0, $DISPLAY_LIMIT),
            'perhatian' => array_slice(array_reverse($santriProgressList), 0, $DISPLAY_LIMIT),
            default     => array_slice($santriProgressList, 0, $DISPLAY_LIMIT),
        };
        $totalSantriFiltered = count($santriProgressList);

        // --- Status akses input capaian santri ---
        $capaianAccessOpen   = CapaianAccessService::isOpen();
        $capaianAccessConfig = CapaianAccessService::getConfig();

        return view('admin.capaian.dashboard', compact(
            'semesters', 'allSemestersOrdered', 'selectedSemester', 'semesterAktif',
            'kelas', 'kelasList', 'kelasModels', 'santrisAktif', 'santrisKhatam', 'materis',
            'totalSantriAktif', 'rataRataProgress',
            'statistikKategori', 'distribusiProgress',
            'rekapKelas',
            'semesterLabels', 'semesterComparison',
            'materiCompletionRate',
            'bottleneckMateri',
            'projectedByKelas',
            'semesterSummary',
            'santriRingkasan', 'totalSantriFiltered', 'filterSantri',
            'capaianAccessOpen', 'capaianAccessConfig'
        ));
    }

    // =====================================================================
    //  TANDAI / BATAL KHATAM — FIX: DB::table agar tidak truncate enum
    // =====================================================================
    public function tandaiKhatam($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        DB::table('santris')
            ->where('id', $santri->id)
            ->update(['status' => 'Khatam', 'updated_at' => now()]);
        return redirect()->back()
            ->with('success', "Santri {$santri->nama_lengkap} berhasil ditandai sebagai Khatam.");
    }

    public function batalKhatam($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        DB::table('santris')
            ->where('id', $santri->id)
            ->update(['status' => 'Aktif', 'updated_at' => now()]);
        return redirect()->back()
            ->with('success', "Status Khatam santri {$santri->nama_lengkap} berhasil dibatalkan.");
    }

    // =====================================================================
    //  EXPORT RAPOR
    // =====================================================================
    public function exportRapor($id_santri, $id_semester)
    {
        $santri   = Santri::where('id_santri', $id_santri)->with('kelasPrimary.kelas')->firstOrFail();
        $semester = Semester::where('id_semester', $id_semester)->firstOrFail();

        $capaians = Capaian::where('id_santri', $id_santri)
            ->where('id_semester', $id_semester)
            ->with('materi')->orderBy('created_at')->get();

        $allSem       = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
        $curIdx       = $allSem->search(fn($s) => $s->id_semester === $id_semester);
        $prevSemester = $curIdx > 0 ? $allSem[$curIdx - 1] : null;
        $prevCapaians = $prevSemester
            ? Capaian::where('id_santri', $id_santri)
                ->where('id_semester', $prevSemester->id_semester)
                ->with('materi')->get()
            : collect();

        $avgProgress = $capaians->avg('persentase') ?? 0;
        $avgPrev     = $prevCapaians->avg('persentase') ?? 0;
        $selesai     = $capaians->where('persentase', '>=', 100)->count();
        $totalMateri = $capaians->count();

        $perKategori = [];
        foreach (["Al-Qur'an", 'Hadist', 'Materi Tambahan'] as $kat) {
            $katCap  = $capaians->filter(fn($c) => $c->materi && $c->materi->kategori === $kat);
            $katPrev = $prevCapaians->filter(fn($c) => $c->materi && $c->materi->kategori === $kat);
            $perKategori[$kat] = [
                'avg'     => round($katCap->avg('persentase') ?? 0, 2),
                'prev'    => round($katPrev->avg('persentase') ?? 0, 2),
                'count'   => $katCap->count(),
                'selesai' => $katCap->where('persentase', '>=', 100)->count(),
            ];
        }

        return view('admin.capaian.export-rapor', compact(
            'santri', 'semester', 'capaians', 'prevSemester', 'prevCapaians',
            'avgProgress', 'avgPrev', 'selesai', 'totalMateri', 'perKategori'
        ));
    }

    // =====================================================================
    //  DETAIL MATERI
    // =====================================================================
    public function detailMateri($id_materi, Request $request)
    {
        $materi           = Materi::where('id_materi', $id_materi)->firstOrFail();
        $semesterAktif    = Semester::aktif()->first();
        $selectedSemester = $request->input('id_semester', $semesterAktif?->id_semester);

        $capaians = Capaian::where('id_materi', $id_materi)
            ->when($selectedSemester, fn($q) => $q->where('id_semester', $selectedSemester))
            ->with(['santri.kelasPrimary.kelas', 'semester'])
            ->orderBy('persentase', 'desc')->get();

        $totalSantri        = $capaians->count();
        $rataRataPersentase = $capaians->avg('persentase') ?? 0;
        $santriSelesai      = $capaians->where('persentase', '>=', 100)->count();
        $santriMulai        = $capaians->where('persentase', '>', 0)->where('persentase', '<', 100)->count();

        $distribusi = [
            '0-25%'  => $capaians->whereBetween('persentase', [0, 25])->count(),
            '26-50%' => $capaians->whereBetween('persentase', [26, 50])->count(),
            '51-75%' => $capaians->whereBetween('persentase', [51, 75])->count(),
            '76-99%' => $capaians->whereBetween('persentase', [76, 99])->count(),
            '100%'   => $capaians->where('persentase', '>=', 100)->count(),
        ];

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.capaian.detail-materi', compact(
            'materi', 'capaians', 'totalSantri', 'rataRataPersentase',
            'santriSelesai', 'santriMulai', 'distribusi', 'semesters', 'selectedSemester'
        ));
    }

    // =====================================================================
    //  API GRAFIK (AJAX)
    // =====================================================================
    public function apiGrafikData(Request $request)
    {
        $type       = $request->input('type', 'kategori');
        $idSemester = $request->input('id_semester');
        $kelas      = $request->input('kelas');

        $query = Capaian::with(['santri', 'materi']);
        if ($idSemester) $query->bySemester($idSemester);
        if ($kelas) {
            $query->whereHas('santri', fn($q) => $q->kelasByName($kelas));
        }

        $data = [];
        switch ($type) {
            case 'kategori':
                $data = [
                    'labels'   => ["Al-Qur'an", 'Hadist', 'Materi Tambahan'],
                    'datasets' => [[
                        'label'           => 'Rata-rata Progress (%)',
                        'data'            => [
                            $query->clone()->byKategori("Al-Qur'an")->avg('persentase') ?? 0,
                            $query->clone()->byKategori('Hadist')->avg('persentase') ?? 0,
                            $query->clone()->byKategori('Materi Tambahan')->avg('persentase') ?? 0,
                        ],
                        'backgroundColor' => ['rgba(111,186,157,0.8)', 'rgba(129,198,232,0.8)', 'rgba(255,213,107,0.8)'],
                    ]]
                ];
                break;
            case 'distribusi':
                $capaians = $query->get();
                $data = [
                    'labels'   => ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
                    'datasets' => [[
                        'label'           => 'Jumlah Santri',
                        'data'            => [
                            $capaians->whereBetween('persentase', [0, 25])->count(),
                            $capaians->whereBetween('persentase', [26, 50])->count(),
                            $capaians->whereBetween('persentase', [51, 75])->count(),
                            $capaians->whereBetween('persentase', [76, 99])->count(),
                            $capaians->where('persentase', '>=', 100)->count(),
                        ],
                        'backgroundColor' => [
                            'rgba(255,139,148,0.8)', 'rgba(255,171,145,0.8)',
                            'rgba(255,213,107,0.8)', 'rgba(129,198,232,0.8)',
                            'rgba(111,186,157,0.8)',
                        ],
                    ]]
                ];
                break;
            case 'trend':
                $semesters  = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
                $labels     = [];
                $dataPoints = [];
                foreach ($semesters as $semester) {
                    $labels[]     = $semester->nama_semester;
                    $dataPoints[] = round(
                        Capaian::where('id_semester', $semester->id_semester)
                            ->when($kelas, fn($q) => $q->whereHas('santri', fn($sq) => $sq->kelasByName($kelas)))
                            ->avg('persentase') ?? 0, 2
                    );
                }
                $data = [
                    'labels'   => $labels,
                    'datasets' => [[
                        'label'           => 'Rata-rata Progress (%)',
                        'data'            => $dataPoints,
                        'borderColor'     => 'rgba(111,186,157,1)',
                        'backgroundColor' => 'rgba(111,186,157,0.2)',
                        'tension'         => 0.4,
                    ]]
                ];
                break;
        }

        return response()->json($data);
    }

    // =====================================================================
    //  KELOLA AKSES INPUT CAPAIAN OLEH SANTRI
    // =====================================================================

    /**
     * Halaman pengaturan akses input capaian santri.
     * GET /admin/capaian/akses-santri
     */
    public function kelolaAksesSantri()
    {
        $config        = CapaianAccessService::getConfig();
        $isOpen        = CapaianAccessService::isOpen();
        $sisaWaktu     = CapaianAccessService::getSisaWaktu();
        $semesters     = Semester::orderBy('tahun_ajaran', 'desc')->get();
        $semesterAktif = Semester::aktif()->first();

        return view('admin.capaian.akses-santri', compact(
            'config', 'isOpen', 'sisaWaktu', 'semesters', 'semesterAktif'
        ));
    }

    /**
     * Buka akses input capaian untuk santri.
     * POST /admin/capaian/akses-santri/buka
     */
    public function bukaAksesSantri(Request $request)
    {
        $request->validate([
            'id_semester' => 'nullable|exists:semester,id_semester',
            'durasi_jam'  => 'nullable|integer|min:1|max:720',
            'catatan'     => 'nullable|string|max:255',
        ]);

        CapaianAccessService::open([
            'opened_by'   => auth()->user()->name,
            'id_semester' => $request->id_semester,
            'durasi_jam'  => $request->durasi_jam,
            'catatan'     => $request->catatan,
        ]);

        return redirect()->route('admin.capaian.akses-santri')
            ->with('success', 'Akses input capaian untuk santri berhasil dibuka.');
    }

    /**
     * Tutup akses input capaian.
     * POST /admin/capaian/akses-santri/tutup
     */
    public function tutupAksesSantri()
    {
        CapaianAccessService::close();

        return redirect()->route('admin.capaian.akses-santri')
            ->with('success', 'Akses input capaian untuk santri berhasil ditutup.');
    }
}