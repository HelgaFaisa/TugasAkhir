<?php
// app/Http/Controllers/Santri/SantriBeritaController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Santri;
use App\Models\SantriKelas;
use Illuminate\Http\Request;

class SantriBeritaController extends Controller
{
    // -- Helper: Ambil id_santri dari akun yang login --
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    /**
     * Tampilkan daftar berita yang bisa diakses santri
     */
    public function index(Request $request)
    {
        $idSantri = $this->getSantriId();

        $santri = Santri::where('id_santri', $idSantri)
            ->select('id_santri')
            ->firstOrFail();

        // -- Ambil id kelas santri --
        $kelasIds = SantriKelas::where('id_santri', $santri->id_santri)
            ->pluck('id_kelas')->toArray();

        $berita = Berita::query()
            ->select(['id', 'id_berita', 'judul', 'konten', 'penulis', 'gambar', 'created_at'])
            ->where('status', 'published')
            ->where(function($query) use ($kelasIds) {
                $query->where('target_berita', 'semua');

                if (!empty($kelasIds)) {
                    $query->orWhere(function($q) use ($kelasIds) {
                        $q->where('target_berita', 'kelas_tertentu');
                        foreach ($kelasIds as $kelasId) {
                            $q->orWhereJsonContains('target_kelas', $kelasId);
                        }
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('santri.berita.index', compact('berita', 'santri'));
    }

    /**
     * Tampilkan detail berita
     */
    public function show($id_berita)
    {
        $idSantri = $this->getSantriId();

        $santri = Santri::where('id_santri', $idSantri)
            ->select('id_santri')
            ->firstOrFail();

        $kelasIds = SantriKelas::where('id_santri', $santri->id_santri)
            ->pluck('id_kelas')->toArray();

        $berita = Berita::where('id_berita', $id_berita)
            ->where('status', 'published')
            ->where(function($query) use ($kelasIds) {
                $query->where('target_berita', 'semua');

                if (!empty($kelasIds)) {
                    $query->orWhere(function($q) use ($kelasIds) {
                        $q->where('target_berita', 'kelas_tertentu');
                        foreach ($kelasIds as $kelasId) {
                            $q->orWhereJsonContains('target_kelas', $kelasId);
                        }
                    });
                }
            })
            ->firstOrFail();

        return view('santri.berita.show', compact('berita', 'santri'));
    }
}