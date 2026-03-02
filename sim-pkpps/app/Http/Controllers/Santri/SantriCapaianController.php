<?php
// app/Http/Controllers/Santri/SantriCapaianController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Capaian;
use App\Models\Santri;
use App\Models\Semester;
use App\Services\CapaianAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SantriCapaianController extends Controller
{
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    public function index(Request $request)
    {
        $idSantri = $this->getSantriId();

        // Ambil data santri
        $santri = Cache::remember("santri_{$idSantri}_profile", 600, function () use ($idSantri) {
            return Santri::where('id_santri', $idSantri)
                ->with(['kelasPrimary.kelas'])
                ->select('id_santri', 'nama_lengkap', 'nis', 'status')
                ->firstOrFail();
        });

        $semesterAktif    = Semester::aktif()->first();
        $selectedSemester = $request->input('id_semester',
            $semesterAktif ? $semesterAktif->id_semester : null
        );

        // Capaian untuk tab Ringkasan / Daftar / Grafik (filter semester)
        $query = Capaian::with([
                'materi:id_materi,nama_kitab,kategori,total_halaman,halaman_mulai,halaman_akhir',
                'semester:id_semester,nama_semester',
            ])
            ->where('id_santri', $idSantri)
            ->select('id', 'id_capaian', 'id_santri', 'id_materi',
                     'id_semester', 'halaman_selesai', 'persentase', 'tanggal_input');

        if ($selectedSemester) {
            $query->where('id_semester', $selectedSemester);
        }

        $capaians = $query->orderBy('tanggal_input', 'desc')->get();

        // Statistik umum
        $totalCapaian       = $capaians->count();
        $rataRataPersentase = $capaians->avg('persentase') ?? 0;
        $materiSelesai      = $capaians->where('persentase', '>=', 100)->count();

        // Statistik per kategori
        $statistikKategori = [
            "Al-Qur'an"       => ['count' => 0, 'avg' => 0, 'selesai' => 0],
            'Hadist'           => ['count' => 0, 'avg' => 0, 'selesai' => 0],
            'Materi Tambahan'  => ['count' => 0, 'avg' => 0, 'selesai' => 0],
        ];

        foreach ($capaians as $capaian) {
            $kat = $capaian->materi->kategori ?? 'Materi Tambahan';
            if (!isset($statistikKategori[$kat])) continue;
            $statistikKategori[$kat]['count']++;
            $statistikKategori[$kat]['avg'] += $capaian->persentase;
            if ($capaian->persentase >= 100) $statistikKategori[$kat]['selesai']++;
        }
        foreach ($statistikKategori as $kat => $data) {
            if ($data['count'] > 0) {
                $statistikKategori[$kat]['avg'] = round($data['avg'] / $data['count'], 2);
            }
        }

        // Distribusi persentase
        $distribusiPersentase = [
            '0-25%'  => $capaians->filter(fn($c) => $c->persentase >= 0  && $c->persentase <= 25)->count(),
            '26-50%' => $capaians->filter(fn($c) => $c->persentase > 25  && $c->persentase <= 50)->count(),
            '51-75%' => $capaians->filter(fn($c) => $c->persentase > 50  && $c->persentase <= 75)->count(),
            '76-99%' => $capaians->filter(fn($c) => $c->persentase > 75  && $c->persentase < 100)->count(),
            '100%'   => $capaians->where('persentase', '>=', 100)->count(),
        ];

        // PREDIKSI: ambil SEMUA capaian tanpa filter semester
        $allCapaians = Capaian::with([
                'materi:id_materi,nama_kitab,kategori',
                'semester:id_semester,nama_semester,tahun_ajaran,periode',
            ])
            ->where('id_santri', $idSantri)
            ->select('id', 'id_santri', 'id_materi', 'id_semester', 'persentase')
            ->get();

        // Susun history per semester (urut cronologis)
        $allSemesters = Semester::orderBy('tahun_ajaran')->orderBy('periode')->get();

        $historyData = [];
        foreach ($allSemesters as $sem) {
            $semCap = $allCapaians->where('id_semester', $sem->id_semester);
            if ($semCap->isNotEmpty()) {
                $historyData[] = [
                    'sem' => $sem->nama_semester,
                    'avg' => round($semCap->avg('persentase'), 2),
                ];
            }
        }

        // Hitung growth rate (rata-rata kenaikan antar semester)
        $growthRate = 0;
        if (count($historyData) >= 2) {
            $diffs = [];
            for ($i = 1; $i < count($historyData); $i++) {
                $diffs[] = $historyData[$i]['avg'] - $historyData[$i - 1]['avg'];
            }
            $growthRate = round(array_sum($diffs) / count($diffs), 2);
        } elseif (count($historyData) === 1) {
            $growthRate = round($historyData[0]['avg'], 2);
        }

        $progressHistory = [
            'history'      => $historyData,
            'growth_rate'  => $growthRate,
            'all_capaians' => $allCapaians,
        ];

        // Semester dropdown
        $semesters = Semester::select('id_semester', 'nama_semester', 'tahun_ajaran')
            ->orderBy('tahun_ajaran', 'desc')
            ->orderBy('periode', 'desc')
            ->get();

        // Status akses input capaian mandiri
        $capaianAccessOpen   = CapaianAccessService::isOpen();
        $capaianAccessConfig = CapaianAccessService::getConfig();
        $capaianSisaWaktu    = CapaianAccessService::getSisaWaktu();

        return view('santri.capaian.index', compact(
            'santri',
            'capaians',
            'totalCapaian',
            'rataRataPersentase',
            'materiSelesai',
            'statistikKategori',
            'distribusiPersentase',
            'progressHistory',
            'semesters',
            'selectedSemester',
            'semesterAktif',
            'capaianAccessOpen',
            'capaianAccessConfig',
            'capaianSisaWaktu'
        ));
    }

    public function show($id)
    {
        $idSantri = $this->getSantriId();

        $capaian = Capaian::with([
            'materi:id_materi,nama_kitab,kategori,halaman_mulai,halaman_akhir,total_halaman',
            'semester:id_semester,nama_semester,tahun_ajaran',
            'santri:id_santri,nama_lengkap,nis',
        ])
        ->where('id_santri', $idSantri)
        ->findOrFail($id);

        return view('santri.capaian.show', compact('capaian'));
    }
}