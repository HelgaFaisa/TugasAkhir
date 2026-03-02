<?php
// app/Http/Controllers/Santri/SantriPelanggaranController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPelanggaran;
use App\Models\KategoriPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SantriPelanggaranController extends Controller
{
    // -- Helper: Ambil id_santri dari akun yang login --
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    /**
     * Tampilkan daftar riwayat pelanggaran santri yang sedang login
     */
    public function index(Request $request)
    {
        $idSantri = $this->getSantriId();

        // -- Query riwayat pelanggaran dengan relasi --
        $query = RiwayatPelanggaran::with(['kategori:id,id_kategori,nama_pelanggaran,poin'])
            ->where('id_santri', $idSantri)
            ->select([
                'id',
                'id_riwayat',
                'id_santri',
                'id_kategori',
                'tanggal',
                'poin',
                'keterangan',
                'created_at'
            ]);

        // -- Filter berdasarkan tanggal --
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // -- Filter bulan ini --
        if ($request->has('bulan_ini') && $request->bulan_ini == '1') {
            $query->bulanIni();
        }

        // -- Urutkan dari terbaru --
        $riwayat = $query->terbaru()->paginate(15);

        // -- Statistik pelanggaran santri --
        $totalPelanggaran = RiwayatPelanggaran::where('id_santri', $idSantri)->count();
        $totalPoin = RiwayatPelanggaran::where('id_santri', $idSantri)->sum('poin');
        $pelanggaranBulanIni = RiwayatPelanggaran::where('id_santri', $idSantri)
            ->bulanIni()
            ->count();

        return view('santri.pelanggaran.index', compact(
            'riwayat',
            'totalPelanggaran',
            'totalPoin',
            'pelanggaranBulanIni'
        ));
    }

    /**
     * Tampilkan detail satu riwayat pelanggaran
     */
    public function show(RiwayatPelanggaran $riwayatPelanggaran)
    {
        // -- Validasi: pastikan pelanggaran milik santri yang login --
        if ($riwayatPelanggaran->id_santri !== $this->getSantriId()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        // -- Load relasi kategori --
        $riwayatPelanggaran->load('kategori:id,id_kategori,nama_pelanggaran,poin');

        return view('santri.pelanggaran.show', compact('riwayatPelanggaran'));
    }

    /**
     * Tampilkan daftar semua kategori pelanggaran beserta poinnya
     */
    public function kategoriList()
    {
        // -- Cache daftar kategori selama 1 jam --
        $kategoriList = Cache::remember('kategori_pelanggaran_list', 3600, function () {
            return KategoriPelanggaran::select([
                'id',
                'id_kategori',
                'nama_pelanggaran',
                'poin'
            ])
            ->orderBy('poin', 'desc')
            ->orderBy('nama_pelanggaran', 'asc')
            ->get();
        });

        return view('santri.pelanggaran.kategori', compact('kategoriList'));
    }
}