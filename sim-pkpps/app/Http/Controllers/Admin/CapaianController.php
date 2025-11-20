<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Capaian;
use App\Models\Santri;
use App\Models\Materi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CapaianController extends Controller
{
    /**
     * Display a listing of capaian
     */
    public function index(Request $request)
    {
        $query = Capaian::with(['santri', 'materi', 'semester']);

        // Filter santri
        if ($request->filled('id_santri')) {
            $query->bySantri($request->id_santri);
        }

        // Filter semester
        if ($request->filled('id_semester')) {
            $query->bySemester($request->id_semester);
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->byKategori($request->kategori);
        }

        $capaians = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        // Data untuk filter
        $santris = Santri::aktif()->orderBy('nama_lengkap')->get();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.capaian.index', compact('capaians', 'santris', 'semesters'));
    }

    /**
     * Show the form for creating new capaian
     */
    public function create(Request $request)
    {
        // Get santri list
        $santris = Santri::aktif()
            ->select('id', 'id_santri', 'nis', 'nama_lengkap', 'kelas')
            ->orderBy('nama_lengkap')
            ->get();

        // Get semester aktif
        $semesterAktif = Semester::aktif()->first();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        // Jika ada pre-selected santri
        $selectedSantri = null;
        $materiOptions = [];
        
        if ($request->filled('id_santri')) {
            $selectedSantri = Santri::where('id_santri', $request->id_santri)->first();
            if ($selectedSantri) {
                // Get materi sesuai kelas santri
                $materiOptions = Materi::where('kelas', $selectedSantri->kelas)
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
        $santri = Santri::where('id_santri', $request->id_santri)->first();
        
        if (!$santri) {
            return response()->json(['error' => 'Santri tidak ditemukan'], 404);
        }

        $materis = Materi::where('kelas', $santri->kelas)
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
     * Store a newly created capaian
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

        // Check duplikasi
        $existing = Capaian::where('id_santri', $validated['id_santri'])
            ->where('id_materi', $validated['id_materi'])
            ->where('id_semester', $validated['id_semester'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Capaian untuk santri, materi, dan semester ini sudah ada. Silakan edit data yang ada.');
        }

        Capaian::create($validated);

        return redirect()->route('admin.capaian.index')
            ->with('success', 'Capaian berhasil ditambahkan.');
    }

    /**
     * Display the specified capaian
     */
    public function show(Capaian $capaian)
    {
        $capaian->load(['santri', 'materi', 'semester']);
        
        return view('admin.capaian.show', compact('capaian'));
    }

    /**
     * Show the form for editing the specified capaian
     */
    public function edit(Capaian $capaian)
    {
        $capaian->load(['santri', 'materi', 'semester']);
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
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        
        $query = Capaian::with(['materi', 'semester'])
            ->bySantri($id_santri);

        // Filter semester
        if ($request->filled('id_semester')) {
            $query->bySemester($request->id_semester);
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
 * Dashboard capaian dengan grafik
 */
public function dashboard(Request $request)
{
    // Get filter inputs
    $idSantri = $request->input('id_santri');
    $idSemester = $request->input('id_semester');
    $kelas = $request->input('kelas');

    // Get semester aktif sebagai default
    $semesterAktif = Semester::aktif()->first();
    $selectedSemester = $idSemester ?: ($semesterAktif ? $semesterAktif->id_semester : null);

    // Data untuk filter
    $santris = Santri::aktif()->orderBy('nama_lengkap')->get();
    $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

    // Build query capaian
    $query = Capaian::with(['santri', 'materi', 'semester']);

    if ($idSantri) {
        $query->bySantri($idSantri);
    }

    if ($selectedSemester) {
        $query->bySemester($selectedSemester);
    }

    if ($kelas) {
        $query->whereHas('santri', function($q) use ($kelas) {
            $q->where('kelas', $kelas);
        });
    }

    // Get data
    $capaians = $query->get();

    // Statistik Umum
    $totalCapaian = $capaians->count();
    $totalSantri = $capaians->pluck('id_santri')->unique()->count();
    $rataRataPersentase = $capaians->avg('persentase') ?? 0;
    $capaianSelesai = $capaians->where('persentase', '>=', 100)->count();

    // Statistik per Kategori
    $statistikKategori = [
        'Al-Qur\'an' => [
            'count' => 0,
            'avg' => 0,
            'selesai' => 0,
        ],
        'Hadist' => [
            'count' => 0,
            'avg' => 0,
            'selesai' => 0,
        ],
        'Materi Tambahan' => [
            'count' => 0,
            'avg' => 0,
            'selesai' => 0,
        ],
    ];

    foreach ($capaians as $capaian) {
        $kategori = $capaian->materi->kategori;
        $statistikKategori[$kategori]['count']++;
        $statistikKategori[$kategori]['avg'] += $capaian->persentase;
        if ($capaian->persentase >= 100) {
            $statistikKategori[$kategori]['selesai']++;
        }
    }

    // Calculate average
    foreach ($statistikKategori as $kategori => $data) {
        if ($data['count'] > 0) {
            $statistikKategori[$kategori]['avg'] = $data['avg'] / $data['count'];
        }
    }

    // Data untuk grafik distribusi persentase
    $distribusiPersentase = [
        '0-25%' => $capaians->whereBetween('persentase', [0, 25])->count(),
        '26-50%' => $capaians->whereBetween('persentase', [26, 50])->count(),
        '51-75%' => $capaians->whereBetween('persentase', [51, 75])->count(),
        '76-99%' => $capaians->whereBetween('persentase', [76, 99])->count(),
        '100%' => $capaians->where('persentase', '>=', 100)->count(),
    ];

    // Top 10 Santri dengan Progress Tertinggi
    $topSantri = Capaian::select('id_santri', DB::raw('AVG(persentase) as rata_rata'))
        ->when($selectedSemester, function($q) use ($selectedSemester) {
            return $q->where('id_semester', $selectedSemester);
        })
        ->when($kelas, function($q) use ($kelas) {
            return $q->whereHas('santri', function($query) use ($kelas) {
                $query->where('kelas', $kelas);
            });
        })
        ->groupBy('id_santri')
        ->orderBy('rata_rata', 'desc')
        ->limit(10)
        ->with('santri')
        ->get();

    // Materi dengan Progress Terendah
    $materiTerendah = Capaian::select('id_materi', DB::raw('AVG(persentase) as rata_rata'), DB::raw('COUNT(*) as jumlah_santri'))
        ->when($selectedSemester, function($q) use ($selectedSemester) {
            return $q->where('id_semester', $selectedSemester);
        })
        ->groupBy('id_materi')
        ->having('rata_rata', '<', 50)
        ->orderBy('rata_rata', 'asc')
        ->limit(5)
        ->with('materi')
        ->get();

    return view('admin.capaian.dashboard', compact(
        'santris',
        'semesters',
        'semesterAktif',
        'selectedSemester',
        'idSantri',
        'kelas',
        'totalCapaian',
        'totalSantri',
        'rataRataPersentase',
        'capaianSelesai',
        'statistikKategori',
        'distribusiPersentase',
        'topSantri',
        'materiTerendah'
    ));
}

/**
 * Rekap capaian per kelas
 */
public function rekapKelas(Request $request)
{
    $kelas = $request->input('kelas', 'Lambatan');
    $idSemester = $request->input('id_semester');

    $semesterAktif = Semester::aktif()->first();
    $selectedSemester = $idSemester ?: ($semesterAktif ? $semesterAktif->id_semester : null);

    // Get santri per kelas
    $santris = Santri::where('kelas', $kelas)
        ->where('status', 'Aktif')
        ->orderBy('nama_lengkap')
        ->get();

    // Get capaian per santri
    $rekapData = [];
    foreach ($santris as $santri) {
        $capaians = Capaian::where('id_santri', $santri->id_santri)
            ->when($selectedSemester, function($q) use ($selectedSemester) {
                return $q->where('id_semester', $selectedSemester);
            })
            ->with('materi')
            ->get();

        $rataRata = $capaians->avg('persentase') ?? 0;
        $totalMateri = $capaians->count();
        $selesai = $capaians->where('persentase', '>=', 100)->count();

        // Per kategori
        $alquran = $capaians->filter(function($c) {
            return $c->materi->kategori == 'Al-Qur\'an';
        })->avg('persentase') ?? 0;

        $hadist = $capaians->filter(function($c) {
            return $c->materi->kategori == 'Hadist';
        })->avg('persentase') ?? 0;

        $tambahan = $capaians->filter(function($c) {
            return $c->materi->kategori == 'Materi Tambahan';
        })->avg('persentase') ?? 0;

        $rekapData[] = [
            'santri' => $santri,
            'rata_rata' => $rataRata,
            'total_materi' => $totalMateri,
            'selesai' => $selesai,
            'alquran' => $alquran,
            'hadist' => $hadist,
            'tambahan' => $tambahan,
        ];
    }

    // Sort by rata-rata desc
    usort($rekapData, function($a, $b) {
        return $b['rata_rata'] <=> $a['rata_rata'];
    });

    $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

    return view('admin.capaian.rekap-kelas', compact('rekapData', 'kelas', 'semesters', 'selectedSemester'));
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
            ->with(['santri', 'semester'])
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
                $q->where('kelas', $kelas);
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
                                $query->where('kelas', $kelas);
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