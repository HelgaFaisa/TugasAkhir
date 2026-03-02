<?php
// app/Http/Controllers/Santri/SantriKepulanganController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Kepulangan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SantriKepulanganController extends Controller
{
    // -- Helper: Ambil id_santri dari akun yang login --
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    /**
     * Tampilkan riwayat kepulangan santri yang sedang login
     */
    public function index(Request $request)
    {
        $idSantri = $this->getSantriId();

        // -- Ambil data santri (tanpa kolom 'kelas' yang mungkin tidak ada) --
        $santri = Santri::where('id_santri', $idSantri)->firstOrFail();

        // -- Tahun untuk filter --
        $tahunSekarang = $request->filled('tahun') ? $request->tahun : Carbon::now()->year;

        // -- Query riwayat kepulangan --
        $query = Kepulangan::query()
            ->where('id_santri', $santri->id_santri)
            ->whereYear('tanggal_pulang', $tahunSekarang);

        // -- Filter status jika ada --
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // -- Urutkan terbaru dan paginate --
        $riwayatKepulangan = $query->orderBy('tanggal_pulang', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // -- Hitung statistik tahun ini --
        $allKepulanganTahunIni = Kepulangan::where('id_santri', $santri->id_santri)
            ->whereYear('tanggal_pulang', $tahunSekarang)
            ->get();

        $statistik = [
            'total_izin'   => $allKepulanganTahunIni->count(),
            'disetujui'    => $allKepulanganTahunIni->where('status', 'Disetujui')->count(),
            'ditolak'      => $allKepulanganTahunIni->where('status', 'Ditolak')->count(),
            'menunggu'     => $allKepulanganTahunIni->where('status', 'Menunggu')->count(),
            'selesai'      => $allKepulanganTahunIni->where('status', 'Selesai')->count(),
            'total_hari'   => $allKepulanganTahunIni->whereIn('status', ['Disetujui', 'Selesai'])->sum('durasi_izin'),
        ];

        $statistik['sisa_kuota'] = max(0, 12 - $statistik['total_hari']);
        $statistik['over_limit'] = $statistik['total_hari'] > 12;
        $statistik['persen_kuota'] = min(100, round(($statistik['total_hari'] / 12) * 100));

        // -- Cek apakah sedang aktif pulang --
        $sedangPulang = Kepulangan::where('id_santri', $santri->id_santri)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_pulang', '<=', Carbon::today())
            ->whereDate('tanggal_kembali', '>=', Carbon::today())
            ->first();

        // -- Cek apakah ada yang terlambat --
        $terlambat = Kepulangan::where('id_santri', $santri->id_santri)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_kembali', '<', Carbon::today())
            ->first();

        // -- Data untuk filter --
        $statusOptions = [
            'Menunggu' => 'Menunggu Approval',
            'Disetujui' => 'Disetujui',
            'Ditolak' => 'Ditolak',
            'Selesai' => 'Selesai'
        ];

        // -- Tahun options (5 tahun terakhir) --
        $tahunOptions = range(Carbon::now()->year, Carbon::now()->year - 4);

        return view('santri.kepulangan.index', compact(
            'riwayatKepulangan',
            'santri',
            'statistik',
            'statusOptions',
            'tahunOptions',
            'tahunSekarang',
            'sedangPulang',
            'terlambat'
        ));
    }

    /**
     * Tampilkan detail kepulangan
     */
    public function show($id_kepulangan)
    {
        $idSantri = $this->getSantriId();

        $santri = Santri::where('id_santri', $idSantri)->firstOrFail();

        // -- Ambil data kepulangan dengan validasi kepemilikan --
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)
            ->where('id_santri', $santri->id_santri)
            ->firstOrFail();

        // -- Hitung total hari izin tahun ini --
        $tahunSekarang = Carbon::now()->year;
        $totalHariTahunIni = Kepulangan::where('id_santri', $santri->id_santri)
            ->whereIn('status', ['Disetujui', 'Selesai'])
            ->whereYear('tanggal_pulang', $tahunSekarang)
            ->sum('durasi_izin');

        $sisaKuota = max(0, 12 - $totalHariTahunIni);
        $persenKuota = min(100, round(($totalHariTahunIni / 12) * 100));

        // -- Riwayat kepulangan lain tahun ini --
        $riwayatLain = Kepulangan::where('id_santri', $santri->id_santri)
            ->where('id_kepulangan', '!=', $id_kepulangan)
            ->whereYear('tanggal_pulang', $tahunSekarang)
            ->whereIn('status', ['Disetujui', 'Selesai'])
            ->orderBy('tanggal_pulang', 'desc')
            ->limit(5)
            ->get();

        return view('santri.kepulangan.show', compact(
            'kepulangan',
            'santri',
            'totalHariTahunIni',
            'sisaKuota',
            'persenKuota',
            'riwayatLain'
        ));
    }
}