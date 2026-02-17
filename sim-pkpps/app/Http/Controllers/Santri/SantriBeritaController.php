<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Santri;
use App\Models\SantriKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SantriBeritaController extends Controller
{
    /**
     * Tampilkan daftar berita yang bisa diakses santri
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri')
            ->firstOrFail();

        // Ambil id kelas santri
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
        $user = Auth::user();

        $santri = Santri::where('id_santri', $user->role_id)
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