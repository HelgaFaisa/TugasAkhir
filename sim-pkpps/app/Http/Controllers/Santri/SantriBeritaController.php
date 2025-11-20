<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SantriBeritaController extends Controller
{
    /**
     * Tampilkan daftar berita yang bisa diakses santri
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil data santri sekali saja
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'kelas')
            ->firstOrFail();
        
        // Query berita yang published dan sesuai target
        $berita = Berita::query()
            ->select([
                'id',
                'id_berita',
                'judul',
                'konten',
                'penulis',
                'gambar',
                'created_at'
            ])
            ->where('status', 'published')
            ->where(function($query) use ($santri) {
                // Berita untuk semua
                $query->where('target_berita', 'semua')
                    // Atau berita untuk kelas santri ini
                    ->orWhere(function($q) use ($santri) {
                        $q->where('target_berita', 'kelas_tertentu')
                          ->whereJsonContains('target_kelas', $santri->kelas);
                    })
                    // Atau berita khusus untuk santri ini
                    ->orWhereHas('santriTertentu', function($q) use ($santri) {
                        $q->where('santris.id_santri', $santri->id_santri);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Ambil status baca santri untuk setiap berita (efficient query)
        $beritaIds = $berita->pluck('id_berita')->toArray();
        $statusBaca = DB::table('berita_santri')
            ->where('id_santri', $santri->id_santri)
            ->whereIn('id_berita', $beritaIds)
            ->pluck('sudah_dibaca', 'id_berita')
            ->toArray();
        
        // Attach status baca ke collection
        $berita->getCollection()->transform(function($item) use ($statusBaca) {
            $item->sudah_dibaca = $statusBaca[$item->id_berita] ?? false;
            return $item;
        });
        
        return view('santri.berita.index', compact('berita', 'santri'));
    }
    
    /**
     * Tampilkan detail berita dan tandai sebagai sudah dibaca
     */
    public function show($id_berita)
    {
        $user = Auth::user();
        
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'kelas')
            ->firstOrFail();
        
        // Ambil berita dengan validasi akses
        $berita = Berita::where('id_berita', $id_berita)
            ->where('status', 'published')
            ->where(function($query) use ($santri) {
                $query->where('target_berita', 'semua')
                    ->orWhere(function($q) use ($santri) {
                        $q->where('target_berita', 'kelas_tertentu')
                          ->whereJsonContains('target_kelas', $santri->kelas);
                    })
                    ->orWhereHas('santriTertentu', function($q) use ($santri) {
                        $q->where('santris.id_santri', $santri->id_santri);
                    });
            })
            ->firstOrFail();
        
        // Tandai sebagai sudah dibaca (insert or update)
        DB::table('berita_santri')->updateOrInsert(
            [
                'id_berita' => $berita->id_berita,
                'id_santri' => $santri->id_santri
            ],
            [
                'sudah_dibaca' => true,
                'tanggal_baca' => now(),
                'updated_at' => now()
            ]
        );
        
        return view('santri.berita.show', compact('berita', 'santri'));
    }
}