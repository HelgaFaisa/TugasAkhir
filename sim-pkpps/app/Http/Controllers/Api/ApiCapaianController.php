<?php
// app/Http/Controllers/Api/ApiCapaianController.php
// UPDATED: Support sistem kelas baru (kelompok_kelas, kelas, santri_kelas)

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Capaian;
use App\Models\Santri;
use App\Models\SantriKelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiCapaianController extends Controller
{
    /**
     * Helper: Build kelas info dari Santri model (sistem kelas baru)
     * Returns kelas_primary & all_kelas arrays
     */
    private function buildKelasInfo(Santri $santri): array
    {
        // Eager load relasi kelas jika belum loaded
        if (!$santri->relationLoaded('kelasPrimary')) {
            $santri->load('kelasPrimary.kelas.kelompok');
        }
        if (!$santri->relationLoaded('kelasSantri')) {
            $santri->load('kelasSantri.kelas.kelompok');
        }

        // Kelas primary
        $kelasPrimary = null;
        $primaryRelation = $santri->kelasPrimary;
        if ($primaryRelation && $primaryRelation->kelas) {
            $kelas = $primaryRelation->kelas;
            $kelompok = $kelas->kelompok;
            $kelasPrimary = [
                'id_kelas' => $kelas->id ?? null,
                'kode_kelas' => $kelas->kode_kelas ?? null,
                'nama_kelas' => $kelas->nama_kelas ?? 'Belum Ada Kelas',
                'kelompok' => $kelompok ? $kelompok->nama_kelompok : null,
                'id_kelompok' => $kelompok ? $kelompok->id_kelompok : null,
                'tahun_ajaran' => $primaryRelation->tahun_ajaran ?? null,
                'is_primary' => true,
            ];
        }

        // All kelas
        $allKelas = $santri->kelasSantri
            ->filter(fn($sk) => $sk->kelas !== null)
            ->map(function ($sk) {
                $kelas = $sk->kelas;
                $kelompok = $kelas->kelompok;
                return [
                    'id_kelas' => $kelas->id ?? null,
                    'kode_kelas' => $kelas->kode_kelas ?? null,
                    'nama_kelas' => $kelas->nama_kelas ?? '-',
                    'kelompok' => $kelompok ? $kelompok->nama_kelompok : null,
                    'id_kelompok' => $kelompok ? $kelompok->id_kelompok : null,
                    'tahun_ajaran' => $sk->tahun_ajaran ?? null,
                    'is_primary' => (bool) $sk->is_primary,
                ];
            })->values()->toArray();

        return [
            'kelas_primary' => $kelasPrimary,
            'all_kelas' => $allKelas,
        ];
    }

    /**
     * Helper: Build santri info array with kelas baru
     */
    private function buildSantriInfo(Santri $santri): array
    {
        $kelasData = $this->buildKelasInfo($santri);
        return [
            'id_santri' => $santri->id_santri,
            'nama_lengkap' => $santri->nama_lengkap,
            'kelas' => $santri->kelas_name, // backward compatible string
            'kelas_primary' => $kelasData['kelas_primary'],
            'all_kelas' => $kelasData['all_kelas'],
        ];
    }

    /**
     * Helper: Get peer santri IDs yang sekelas (via santri_kelas pivot)
     */
    private function getPeerSantriIds(Santri $santri, ?string $idSemester = null): array
    {
        $primaryKelasId = $santri->primary_kelas_id;
        if (!$primaryKelasId) {
            return [$santri->id_santri]; // hanya diri sendiri jika tidak punya kelas
        }

        return SantriKelas::where('id_kelas', $primaryKelasId)
            ->pluck('id_santri')
            ->unique()
            ->toArray();
    }

    /**
     * GET OVERVIEW CAPAIAN SANTRI
     * Endpoint: GET /api/v1/capaian/overview
     */
    public function overview(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;

            $santri = Santri::with(['kelasPrimary.kelas.kelompok', 'kelasSantri.kelas.kelompok'])
                ->where('id_santri', $idSantri)
                ->first();

            if (!$santri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan. ID: ' . $idSantri,
                ], 404);
            }

            $semesterAktif = Semester::aktif()->first();
            $idSemester = $request->input('id_semester', $semesterAktif?->id_semester);

            $query = Capaian::where('id_santri', $idSantri)
                ->with(['materi', 'semester']);

            if ($idSemester) {
                $query->where('id_semester', $idSemester);
            }

            $capaians = $query->get();

            $capaiansBerisi = $capaians->where('persentase', '>', 0);
            $totalMateri = $capaiansBerisi->count();
            $rataRataProgress = $capaiansBerisi->isEmpty() ? 0 : $capaiansBerisi->avg('persentase');
            $materiSelesai = $capaians->where('persentase', '>=', 100)->count();

            $perKategori = [];
            $kategoriList = ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'];

            foreach ($kategoriList as $kategori) {
                $capaianKategori = $capaians->filter(function($c) use ($kategori) {
                    return $c->materi && $c->materi->kategori === $kategori;
                });

                $capaianKategoriBerisi = $capaianKategori->where('persentase', '>', 0);
                
                $perKategori[] = [
                    'kategori' => $kategori,
                    'icon' => $this->getKategoriIcon($kategori),
                    'color' => $this->getKategoriColor($kategori),
                    'total_materi' => $capaianKategoriBerisi->count(),
                    'rata_rata_progress' => round($capaianKategoriBerisi->isEmpty() ? 0 : $capaianKategoriBerisi->avg('persentase'), 1),
                    'materi_selesai' => $capaianKategori->where('persentase', '>=', 100)->count(),
                ];
            }

            $semesters = Semester::select('id_semester', 'nama_semester', 'tahun_ajaran', 'periode', 'is_active')
                ->orderBy('tahun_ajaran', 'desc')
                ->orderBy('periode', 'desc')
                ->get()
                ->map(function($s) {
                    return [
                        'id_semester' => $s->id_semester,
                        'nama_semester' => $s->nama_semester,
                        'is_aktif' => $s->is_active == 1,
                    ];
                });

            $response = [
                'success' => true,
                'data' => [
                    'santri' => $this->buildSantriInfo($santri),
                    'semester' => [
                        'id_semester' => $idSemester,
                        'nama_semester' => $semesterAktif?->nama_semester ?? 'Semua Semester',
                        'list_semester' => $semesters,
                    ],
                    'statistik_umum' => [
                        'total_materi' => $totalMateri,
                        'rata_rata_progress' => round($rataRataProgress, 1),
                        'materi_selesai' => $materiSelesai,
                    ],
                    'per_kategori' => $perKategori,
                ],
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error('Error di Capaian Overview', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET LIST MATERI PER KATEGORI
     * Endpoint: GET /api/v1/capaian/kategori/{kategori}
     */
    public function listMateriByKategori(Request $request, $kategori)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;

            $validKategori = ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'];
            if (!in_array($kategori, $validKategori)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak valid: ' . $kategori,
                ], 400);
            }

            $santri = Santri::with(['kelasPrimary.kelas.kelompok'])->where('id_santri', $idSantri)->first();
            if (!$santri) {
                return response()->json(['success' => false, 'message' => 'Data santri tidak ditemukan'], 404);
            }

            $semesterAktif = Semester::aktif()->first();
            $idSemester = $request->input('id_semester', $semesterAktif?->id_semester);

            $query = Capaian::where('id_santri', $idSantri)
                ->whereHas('materi', function($q) use ($kategori) {
                    $q->where('kategori', $kategori);
                })
                ->with(['materi', 'semester']);

            if ($idSemester) {
                $query->where('id_semester', $idSemester);
            }

            $capaians = $query->get();

            $materiList = $capaians->map(function($capaian) {
                return [
                    'id_capaian' => $capaian->id_capaian,
                    'materi' => [
                        'id_materi' => $capaian->materi->id_materi,
                        'nama_kitab' => $capaian->materi->nama_kitab,
                        'total_halaman' => $capaian->materi->total_halaman,
                        'halaman_mulai' => $capaian->materi->halaman_mulai,
                        'halaman_akhir' => $capaian->materi->halaman_akhir,
                    ],
                    'progress' => [
                        'halaman_selesai' => $capaian->jumlah_halaman_selesai,
                        'persentase' => round($capaian->persentase, 1),
                        'status' => $this->getStatusCapaian($capaian->persentase),
                        'status_color' => $this->getStatusColor($capaian->persentase),
                    ],
                    'tanggal_input' => $capaian->tanggal_input->format('d M Y'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'kategori' => $kategori,
                    'icon' => $this->getKategoriIcon($kategori),
                    'color' => $this->getKategoriColor($kategori),
                    'total_materi' => $materiList->count(),
                    'materi_list' => $materiList,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error di List Materi by Kategori', [
                'message' => $e->getMessage(),
                'kategori' => $kategori,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET DETAIL CAPAIAN PER MATERI
     * Endpoint: GET /api/v1/capaian/detail/{idCapaian}
     * Now includes kelas_primary in santri info
     */
    public function detailCapaian(Request $request, $idCapaian)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;

            $capaian = Capaian::where('id_capaian', $idCapaian)
                ->where('id_santri', $idSantri)
                ->with(['materi', 'semester', 'santri.kelasPrimary.kelas.kelompok'])
                ->first();

            if (!$capaian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data capaian tidak ditemukan',
                ], 404);
            }

            $halamanArray = $capaian->pages_array;
            
            $breakdown = [
                'halaman_selesai_list' => $halamanArray,
                'jumlah_halaman_selesai' => count($halamanArray),
                'halaman_belum_selesai' => $capaian->materi->total_halaman - count($halamanArray),
                'halaman_selesai_text' => $capaian->halaman_selesai,
            ];

            // Build kelas_primary info
            $kelasPrimary = null;
            if ($capaian->santri) {
                $kelasData = $this->buildKelasInfo($capaian->santri);
                $kelasPrimary = $kelasData['kelas_primary'];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_capaian' => $capaian->id_capaian,
                    'santri_info' => $capaian->santri ? $this->buildSantriInfo($capaian->santri) : null,
                    'materi' => [
                        'id_materi' => $capaian->materi->id_materi,
                        'kategori' => $capaian->materi->kategori,
                        'nama_kitab' => $capaian->materi->nama_kitab,
                        'kelas' => $capaian->materi->kelas,
                        'total_halaman' => $capaian->materi->total_halaman,
                        'halaman_mulai' => $capaian->materi->halaman_mulai,
                        'halaman_akhir' => $capaian->materi->halaman_akhir,
                        'deskripsi' => $capaian->materi->deskripsi,
                    ],
                    'semester' => [
                        'id_semester' => $capaian->semester->id_semester,
                        'nama_semester' => $capaian->semester->nama_semester,
                    ],
                    'progress' => [
                        'persentase' => round($capaian->persentase, 1),
                        'status' => $this->getStatusCapaian($capaian->persentase),
                        'status_color' => $this->getStatusColor($capaian->persentase),
                    ],
                    'breakdown' => $breakdown,
                    'catatan' => $capaian->catatan,
                    'tanggal_input' => $capaian->tanggal_input->format('d F Y'),
                    'last_updated' => $capaian->updated_at->diffForHumans(),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error di Detail Capaian', [
                'message' => $e->getMessage(),
                'id_capaian' => $idCapaian,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET GRAFIK PROGRESS HISTORIS
     * Endpoint: GET /api/v1/capaian/grafik-progress
     */
    public function grafikProgress(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;

            $semesters = Semester::orderBy('tahun_ajaran')
                ->orderBy('periode')
                ->get();

            $dataGrafik = [];

            foreach ($semesters as $semester) {
                $capaians = Capaian::where('id_santri', $idSantri)
                    ->where('id_semester', $semester->id_semester)
                    ->where('persentase', '>', 0)
                    ->get();

                if ($capaians->count() > 0) {
                    $dataGrafik[] = [
                        'semester' => $semester->nama_semester,
                        'id_semester' => $semester->id_semester,
                        'rata_rata_progress' => round($capaians->avg('persentase'), 1),
                        'total_materi' => $capaians->count(),
                        'materi_selesai' => $capaians->where('persentase', '>=', 100)->count(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $dataGrafik,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error di Grafik Progress', ['message' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET TREND SEMESTER
     * Endpoint: GET /api/v1/capaian/trend-semester
     * Returns progress per semester for line chart visualization
     */
    public function trendSemester(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;

            $santri = Santri::with(['kelasPrimary.kelas.kelompok'])->where('id_santri', $idSantri)->first();
            if (!$santri) {
                return response()->json(['success' => false, 'message' => 'Data santri tidak ditemukan'], 404);
            }

            // Load all capaian grouped by semester
            $allCapaian = Capaian::where('id_santri', $idSantri)
                ->with(['materi', 'semester'])
                ->where('persentase', '>', 0)
                ->get();

            $semesters = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
            $kategoriList = ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'];

            $trendData = [];
            foreach ($semesters as $sem) {
                $semCapaian = $allCapaian->where('id_semester', $sem->id_semester);
                if ($semCapaian->isEmpty()) continue;

                $perKat = [];
                foreach ($kategoriList as $kat) {
                    $katCapaian = $semCapaian->filter(fn($c) => $c->materi && $c->materi->kategori === $kat);
                    if ($katCapaian->isNotEmpty()) {
                        $perKat[] = [
                            'kategori' => $kat,
                            'rata_rata' => round($katCapaian->avg('persentase'), 1),
                            'total_materi' => $katCapaian->count(),
                            'materi_selesai' => $katCapaian->where('persentase', '>=', 100)->count(),
                        ];
                    }
                }

                $trendData[] = [
                    'id_semester' => $sem->id_semester,
                    'nama_semester' => $sem->nama_semester,
                    'tahun_ajaran' => $sem->tahun_ajaran,
                    'rata_rata_progress' => round($semCapaian->avg('persentase'), 1),
                    'total_materi' => $semCapaian->count(),
                    'materi_selesai' => $semCapaian->where('persentase', '>=', 100)->count(),
                    'per_kategori' => $perKat,
                    'is_aktif' => $sem->is_active == 1,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'santri' => $this->buildSantriInfo($santri),
                    'trend' => $trendData,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error di Trend Semester', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET DASHBOARD CAPAIAN (COMPREHENSIVE)
     * Endpoint: GET /api/v1/capaian/dashboard
     * Single endpoint returning all data for enhanced mobile capaian page
     * UPDATED: Uses new kelas system (santri_kelas pivot table)
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;
            $santri = Santri::with(['kelasPrimary.kelas.kelompok', 'kelasSantri.kelas.kelompok'])
                ->where('id_santri', $idSantri)
                ->first();

            if (!$santri) {
                return response()->json(['success' => false, 'message' => 'Data santri tidak ditemukan'], 404);
            }

            $semesterAktif = Semester::aktif()->first();
            $idSemester = $request->input('id_semester', $semesterAktif?->id_semester);
            $selectedSemester = $idSemester
                ? Semester::where('id_semester', $idSemester)->first()
                : $semesterAktif;

            $allSemesters = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();
            $listSemester = $allSemesters->map(fn($s) => [
                'id_semester' => $s->id_semester,
                'nama_semester' => $s->nama_semester,
                'tahun_ajaran' => $s->tahun_ajaran,
                'periode' => $s->periode,
                'is_aktif' => $s->is_active == 1,
            ])->values();

            // ===== Load ALL capaian santri in one query =====
            $allCapaianSantri = Capaian::where('id_santri', $idSantri)
                ->with(['materi', 'semester'])
                ->get();

            // Current semester capaians
            $currentCapaians = $allCapaianSantri->where('id_semester', $idSemester);
            $currentBerisi = $currentCapaians->where('persentase', '>', 0);
            $totalProgress = $currentBerisi->isEmpty() ? 0 : round($currentBerisi->avg('persentase'), 1);
            $materiSelesaiSemIni = $currentCapaians->where('persentase', '>=', 100)->count();

            // ===== Per Kategori =====
            $kategoriList = ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'];
            $perKategori = [];
            foreach ($kategoriList as $kategori) {
                $capKat = $currentCapaians->filter(fn($c) => $c->materi && $c->materi->kategori === $kategori);
                $capKatBerisi = $capKat->where('persentase', '>', 0);
                $perKategori[] = [
                    'kategori' => $kategori,
                    'icon' => $this->getKategoriIcon($kategori),
                    'color' => $this->getKategoriColor($kategori),
                    'total_materi' => $capKatBerisi->count(),
                    'rata_rata_progress' => round($capKatBerisi->isEmpty() ? 0 : $capKatBerisi->avg('persentase'), 1),
                    'materi_selesai' => $capKat->where('persentase', '>=', 100)->count(),
                ];
            }

            // ===== Semester History =====
            $bySemester = $allCapaianSantri->where('persentase', '>', 0)->groupBy('id_semester');
            $semesterHistory = [];
            foreach ($allSemesters as $sem) {
                if ($bySemester->has($sem->id_semester)) {
                    $semCaps = $bySemester[$sem->id_semester];
                    $semesterHistory[] = [
                        'id_semester' => $sem->id_semester,
                        'nama_semester' => $sem->nama_semester,
                        'rata_rata_progress' => round($semCaps->avg('persentase'), 1),
                        'total_materi' => $semCaps->count(),
                        'materi_selesai' => $semCaps->where('persentase', '>=', 100)->count(),
                        'is_current' => $sem->id_semester === $idSemester,
                    ];
                }
            }

            // ===== Achievements =====
            $achievements = [];
            if ($materiSelesaiSemIni > 0) {
                $achievements[] = ['icon' => 'trophy', 'text' => "Khatam $materiSelesaiSemIni Materi Semester Ini", 'type' => 'khatam'];
            }

            $currentIdx = -1;
            for ($i = 0; $i < count($semesterHistory); $i++) {
                if ($semesterHistory[$i]['id_semester'] === $idSemester) {
                    $currentIdx = $i;
                    break;
                }
            }
            if ($currentIdx > 0) {
                $prevProgress = $semesterHistory[$currentIdx - 1]['rata_rata_progress'];
                $curProgress = $semesterHistory[$currentIdx]['rata_rata_progress'];
                $change = round($curProgress - $prevProgress, 1);
                if ($change > 0) {
                    $achievements[] = ['icon' => 'trending_up', 'text' => "Kenaikan {$change}% dari Semester Lalu", 'type' => 'growth'];
                } elseif ($change < 0) {
                    $achievements[] = ['icon' => 'trending_down', 'text' => "Penurunan " . abs($change) . "% dari Semester Lalu", 'type' => 'decline'];
                }
            }

            // ===== Ranking & Peer Comparison (NEW: via santri_kelas pivot) =====
            $peerSantriIds = $this->getPeerSantriIds($santri, $idSemester);

            $rankings = collect();
            if ($idSemester && count($peerSantriIds) > 1) {
                $rankings = Capaian::whereIn('id_santri', $peerSantriIds)
                    ->where('id_semester', $idSemester)
                    ->where('persentase', '>', 0)
                    ->select('id_santri', DB::raw('AVG(persentase) as avg_progress'))
                    ->groupBy('id_santri')
                    ->orderByDesc('avg_progress')
                    ->get();
            }

            $rank = 0;
            $totalRanked = $rankings->count();
            foreach ($rankings as $i => $r) {
                if ($r->id_santri === $idSantri) {
                    $rank = $i + 1;
                    break;
                }
            }

            if ($rank > 0 && $totalRanked > 1) {
                $achievements[] = [
                    'icon' => $rank <= 3 ? 'star' : 'emoji_events',
                    'text' => "Peringkat $rank dari $totalRanked di Kelas",
                    'type' => 'rank',
                ];
            }

            // Peer comparison per kategori (NEW: via santri_kelas pivot)
            $peerComparison = [];
            if ($idSemester && count($peerSantriIds) > 1) {
                $peerData = Capaian::whereIn('id_santri', $peerSantriIds)
                    ->join('materi', 'capaian.id_materi', '=', 'materi.id_materi')
                    ->where('capaian.id_semester', $idSemester)
                    ->where('capaian.persentase', '>', 0)
                    ->groupBy('materi.kategori')
                    ->select('materi.kategori', DB::raw('AVG(capaian.persentase) as kelas_avg'))
                    ->get()
                    ->keyBy('kategori');

                foreach ($kategoriList as $kategori) {
                    $santriKatBerisi = $currentCapaians->filter(fn($c) => $c->materi && $c->materi->kategori === $kategori && $c->persentase > 0);
                    $santriAvg = $santriKatBerisi->isEmpty() ? 0 : round($santriKatBerisi->avg('persentase'), 1);
                    $kelasAvg = isset($peerData[$kategori]) ? round($peerData[$kategori]->kelas_avg, 1) : 0;

                    $peerComparison[] = [
                        'kategori' => $kategori,
                        'icon' => $this->getKategoriIcon($kategori),
                        'color' => $this->getKategoriColor($kategori),
                        'santri_progress' => $santriAvg,
                        'kelas_avg' => $kelasAvg,
                    ];
                }
            } else {
                // No peers, just show santri data
                foreach ($kategoriList as $kategori) {
                    $santriKatBerisi = $currentCapaians->filter(fn($c) => $c->materi && $c->materi->kategori === $kategori && $c->persentase > 0);
                    $santriAvg = $santriKatBerisi->isEmpty() ? 0 : round($santriKatBerisi->avg('persentase'), 1);

                    $peerComparison[] = [
                        'kategori' => $kategori,
                        'icon' => $this->getKategoriIcon($kategori),
                        'color' => $this->getKategoriColor($kategori),
                        'santri_progress' => $santriAvg,
                        'kelas_avg' => 0,
                    ];
                }
            }

            // ===== Materi Status =====
            $materiStatus = $currentCapaians->map(function ($c) {
                $status = 'belum_mulai';
                if ($c->persentase >= 100) $status = 'selesai';
                elseif ($c->persentase > 0) $status = 'progres';

                return [
                    'id_capaian' => $c->id_capaian,
                    'nama_kitab' => $c->materi->nama_kitab ?? '-',
                    'kategori' => $c->materi->kategori ?? '-',
                    'persentase' => round($c->persentase, 1),
                    'status' => $status,
                    'status_label' => $this->getStatusCapaian($c->persentase),
                    'status_color' => $this->getStatusColor($c->persentase),
                    'icon' => $this->getKategoriIcon($c->materi->kategori ?? ''),
                    'color' => $this->getKategoriColor($c->materi->kategori ?? ''),
                ];
            })->sortByDesc('persentase')->values();

            // ===== Rapor Summary =====
            $raporSummary = [
                'total_progress' => $totalProgress,
                'total_materi' => $currentBerisi->count(),
                'materi_selesai' => $materiSelesaiSemIni,
                'perubahan' => 0,
                'trend' => 'tetap',
                'predikat' => $this->getPredikat($totalProgress),
            ];

            if ($currentIdx > 0) {
                $prevProg = $semesterHistory[$currentIdx - 1]['rata_rata_progress'];
                $curProg = $semesterHistory[$currentIdx]['rata_rata_progress'];
                $raporSummary['perubahan'] = round($curProg - $prevProg, 1);
                $raporSummary['trend'] = $curProg > $prevProg ? 'naik' : ($curProg < $prevProg ? 'turun' : 'tetap');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'role' => $user->role,
                    'santri' => $this->buildSantriInfo($santri),
                    'semester' => [
                        'id_semester' => $selectedSemester?->id_semester,
                        'nama_semester' => $selectedSemester?->nama_semester ?? 'Tidak Diketahui',
                        'tahun_ajaran' => $selectedSemester?->tahun_ajaran,
                    ],
                    'list_semester' => $listSemester,
                    'current_progress' => [
                        'total_progress' => $totalProgress,
                        'total_materi' => $currentBerisi->count(),
                        'materi_selesai' => $materiSelesaiSemIni,
                        'per_kategori' => $perKategori,
                    ],
                    'semester_history' => array_values($semesterHistory),
                    'achievements' => $achievements,
                    'materi_status' => $materiStatus,
                    'peer_comparison' => $peerComparison,
                    'rapor_summary' => $raporSummary,
                    'rank' => $rank > 0 ? ['position' => $rank, 'total' => $totalRanked] : null,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error di Capaian Dashboard', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    private function getPredikat($progress)
    {
        if ($progress >= 90) return 'Baik Sekali';
        if ($progress >= 75) return 'Baik';
        if ($progress >= 50) return 'Cukup';
        return 'Perlu Perhatian';
    }

    private function getKategoriIcon($kategori)
    {
        $icons = [
            'Al-Qur\'an' => 'book_quran',
            'Hadist' => 'scroll',
            'Materi Tambahan' => 'book',
        ];

        return $icons[$kategori] ?? 'book';
    }

    private function getKategoriColor($kategori)
    {
        $colors = [
            'Al-Qur\'an' => '#6FBAA5',
            'Hadist' => '#81C6E8',
            'Materi Tambahan' => '#FFD56B',
        ];

        return $colors[$kategori] ?? '#6B7280';
    }

    private function getStatusCapaian($persentase)
    {
        if ($persentase >= 100) return 'Selesai';
        if ($persentase >= 75) return 'Hampir Selesai';
        if ($persentase >= 50) return 'Dalam Progress';
        if ($persentase >= 25) return 'Baru Mulai';
        if ($persentase > 0) return 'Mulai';
        return 'Belum Mulai';
    }

    private function getStatusColor($persentase)
    {
        if ($persentase >= 100) return '#10B981';
        if ($persentase >= 75) return '#3B82F6';
        if ($persentase >= 50) return '#F59E0B';
        if ($persentase >= 25) return '#EF4444';
        return '#6B7280';
    }
}