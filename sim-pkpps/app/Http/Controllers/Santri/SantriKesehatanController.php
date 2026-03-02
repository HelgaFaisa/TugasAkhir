<?php
// app/Http/Controllers/Santri/SantriKesehatanController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\KesehatanSantri;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SantriKesehatanController extends Controller
{
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    /**
     * Tampilkan riwayat kesehatan santri yang sedang login
     */
    public function index(Request $request)
    {
        $idSantri = $this->getSantriId();

        // ✅ Fix: hapus 'kelas' dari select, tambah eager load kelasPrimary
        $santri = Santri::with('kelasPrimary.kelas')
            ->where('id_santri', $idSantri)
            ->select('id_santri', 'nama_lengkap', 'jenis_kelamin', 'status')
            ->firstOrFail();

        // -- Tentukan range tanggal --
        $tanggalDari = $request->filled('tanggal_dari')
            ? Carbon::parse($request->tanggal_dari)
            : Carbon::now()->startOfMonth();

        $tanggalSampai = $request->filled('tanggal_sampai')
            ? Carbon::parse($request->tanggal_sampai)
            : Carbon::now()->endOfMonth();

        // -- Validasi tanggal --
        if ($tanggalSampai->lt($tanggalDari)) {
            return back()->withErrors([
                'tanggal_sampai' => 'Tanggal sampai harus lebih besar dari tanggal dari.'
            ])->withInput();
        }

        // -- Statistik berdasarkan filter tanggal --
        $baseQuery = KesehatanSantri::where('id_santri', $idSantri)
            ->whereBetween('tanggal_masuk', [
                $tanggalDari->format('Y-m-d'),
                $tanggalSampai->format('Y-m-d'),
            ]);

        $statistik = [
            'total_kunjungan' => (clone $baseQuery)->count(),
            'sedang_dirawat'  => (clone $baseQuery)->where('status', 'dirawat')->count(),
            'sembuh'          => (clone $baseQuery)->where('status', 'sembuh')->count(),
            'izin'            => (clone $baseQuery)->where('status', 'izin')->count(),
        ];

        // -- Cek apakah SAAT INI sedang dirawat (semua waktu, bukan filter) --
        $sedangDirawatSekarang = KesehatanSantri::where('id_santri', $idSantri)
            ->where('status', 'dirawat')
            ->latest('tanggal_masuk')
            ->first();

        // -- Data grafik: kunjungan per bulan (6 bulan terakhir) --
        $dataGrafik = KesehatanSantri::where('id_santri', $idSantri)
            ->where('tanggal_masuk', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw('YEAR(tanggal_masuk) as tahun'),
                DB::raw('MONTH(tanggal_masuk) as bulan'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "sembuh" THEN 1 ELSE 0 END) as sembuh'),
                DB::raw('SUM(CASE WHEN status = "dirawat" THEN 1 ELSE 0 END) as dirawat'),
                DB::raw('SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->map(fn($item) => [
                'label'   => Carbon::createFromDate($item->tahun, $item->bulan, 1)
                                ->locale('id')->isoFormat('MMM YY'),
                'total'   => $item->total,
                'sembuh'  => $item->sembuh,
                'dirawat' => $item->dirawat,
                'izin'    => $item->izin,
            ]);

        // -- Statistik total keseluruhan (all time) --
        $totalAllTime = KesehatanSantri::where('id_santri', $idSantri)->count();
        $totalHariDirawat = KesehatanSantri::where('id_santri', $idSantri)->get()
            ->sum('lama_dirawat');

        // -- Query riwayat dengan filter --
        $query = KesehatanSantri::where('id_santri', $idSantri)
            ->whereBetween('tanggal_masuk', [
                $tanggalDari->format('Y-m-d'),
                $tanggalSampai->format('Y-m-d'),
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riwayatKesehatan = $query->orderBy('tanggal_masuk', 'desc')
            ->paginate(10)
            ->appends($request->all());

        $statusOptions = [
            'dirawat' => 'Sedang Dirawat',
            'sembuh'  => 'Sembuh',
            'izin'    => 'Izin Sakit',
        ];

        return view('santri.kesehatan.index', compact(
            'riwayatKesehatan',
            'santri',
            'statistik',
            'statusOptions',
            'tanggalDari',
            'tanggalSampai',
            'sedangDirawatSekarang',
            'dataGrafik',
            'totalAllTime',
            'totalHariDirawat'
        ));
    }

    /**
     * Tampilkan detail riwayat kesehatan
     */
    public function show($id)
    {
        $idSantri = $this->getSantriId();

        // ✅ Fix: hapus 'kelas' dari select
        $santri = Santri::with('kelasPrimary.kelas')
            ->where('id_santri', $idSantri)
            ->select('id_santri', 'nama_lengkap', 'jenis_kelamin', 'status')
            ->firstOrFail();

        $kesehatanSantri = KesehatanSantri::where('id', $id)
            ->where('id_santri', $idSantri)
            ->firstOrFail();

        // -- Riwayat lain santri ini (untuk konteks) --
        $riwayatLain = KesehatanSantri::where('id_santri', $idSantri)
            ->where('id', '!=', $id)
            ->orderBy('tanggal_masuk', 'desc')
            ->take(3)
            ->get();

        return view('santri.kesehatan.show', compact(
            'kesehatanSantri',
            'santri',
            'riwayatLain'
        ));
    }
}