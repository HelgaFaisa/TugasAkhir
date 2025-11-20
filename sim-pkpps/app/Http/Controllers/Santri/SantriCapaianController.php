<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Capaian;
use App\Models\Santri;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SantriCapaianController extends Controller
{
    /**
     * Tampilkan daftar capaian santri yang sedang login
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Validasi role
        if (!in_array($user->role, ['santri', 'wali'])) {
            abort(403, 'Unauthorized access');
        }
        
        $idSantri = $user->role_id;
        
        // Cache data santri selama 10 menit
        $santri = Cache::remember("santri_capaian_{$idSantri}", 600, function () use ($idSantri) {
            return Santri::where('id_santri', $idSantri)
                ->select('id_santri', 'nama_lengkap', 'kelas', 'nis')
                ->firstOrFail();
        });
        
        // Get semester aktif
        $semesterAktif = Semester::aktif()->first();
        $selectedSemester = $request->input('id_semester', $semesterAktif?->id_semester);
        
        // Query capaian dengan relasi
        $query = Capaian::with(['materi:id_materi,nama_kitab,kategori,total_halaman', 'semester:id_semester,nama_semester'])
            ->where('id_santri', $idSantri)
            ->select('id', 'id_capaian', 'id_santri', 'id_materi', 'id_semester', 'halaman_selesai', 'persentase', 'tanggal_input');
        
        // Filter semester
        if ($selectedSemester) {
            $query->where('id_semester', $selectedSemester);
        }
        
        $capaians = $query->orderBy('tanggal_input', 'desc')->get();
        
        // Statistik Umum
        $totalCapaian = $capaians->count();
        $rataRataPersentase = $capaians->avg('persentase') ?? 0;
        $materiSelesai = $capaians->where('persentase', '>=', 100)->count();
        
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
        
        // Hitung rata-rata
        foreach ($statistikKategori as $kategori => $data) {
            if ($data['count'] > 0) {
                $statistikKategori[$kategori]['avg'] = $data['avg'] / $data['count'];
            }
        }
        
        // Distribusi persentase untuk chart
        $distribusiPersentase = [
            '0-25%' => $capaians->whereBetween('persentase', [0, 25])->count(),
            '26-50%' => $capaians->whereBetween('persentase', [26, 50])->count(),
            '51-75%' => $capaians->whereBetween('persentase', [51, 75])->count(),
            '76-99%' => $capaians->whereBetween('persentase', [76, 99])->count(),
            '100%' => $capaians->where('persentase', '>=', 100)->count(),
        ];
        
        // Data untuk semester dropdown
        $semesters = Semester::select('id_semester', 'nama_semester', 'tahun_ajaran')
            ->orderBy('tahun_ajaran', 'desc')
            ->orderBy('periode', 'desc')
            ->get();
        
        return view('santri.capaian.index', compact(
            'santri',
            'capaians',
            'totalCapaian',
            'rataRataPersentase',
            'materiSelesai',
            'statistikKategori',
            'distribusiPersentase',
            'semesters',
            'selectedSemester',
            'semesterAktif'
        ));
    }
    
    /**
     * Tampilkan detail capaian tertentu
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['santri', 'wali'])) {
            abort(403, 'Unauthorized access');
        }
        
        $capaian = Capaian::with([
            'materi:id_materi,nama_kitab,kategori,halaman_mulai,halaman_akhir,total_halaman',
            'semester:id_semester,nama_semester,tahun_ajaran',
            'santri:id_santri,nama_lengkap,kelas'
        ])
        ->where('id_santri', $user->role_id)
        ->findOrFail($id);
        
        return view('santri.capaian.show', compact('capaian'));
    }
    
    /**
     * API untuk data grafik (AJAX)
     */
    public function apiGrafikData(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'kategori');
        $idSemester = $request->input('id_semester');
        
        $query = Capaian::with('materi:id_materi,kategori')
            ->where('id_santri', $user->role_id)
            ->select('id', 'id_materi', 'persentase', 'id_semester');
        
        if ($idSemester) {
            $query->where('id_semester', $idSemester);
        }
        
        $capaians = $query->get();
        $data = [];
        
        switch ($type) {
            case 'kategori':
                $avgAlquran = $capaians->filter(fn($c) => $c->materi->kategori == 'Al-Qur\'an')->avg('persentase') ?? 0;
                $avgHadist = $capaians->filter(fn($c) => $c->materi->kategori == 'Hadist')->avg('persentase') ?? 0;
                $avgTambahan = $capaians->filter(fn($c) => $c->materi->kategori == 'Materi Tambahan')->avg('persentase') ?? 0;
                
                $data = [
                    'labels' => ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
                    'datasets' => [[
                        'label' => 'Rata-rata Progress (%)',
                        'data' => [
                            round($avgAlquran, 2),
                            round($avgHadist, 2),
                            round($avgTambahan, 2)
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
                $data = [
                    'labels' => ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
                    'datasets' => [[
                        'label' => 'Jumlah Materi',
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
        }
        
        return response()->json($data);
    }
}