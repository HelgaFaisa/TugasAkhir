<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Santri;
use Illuminate\Http\Request;

class ApiBeritaController extends Controller
{
    /**
     * Get list berita untuk santri yang login
     */
    public function index(Request $request)
    {
        try {
            $idSantri = $request->user()->role_id;

            $santri = Santri::with('kelasPrimary.kelas')->where('id_santri', $idSantri)->first();

            if (!$santri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan',
                ], 404);
            }

            $idKelasSantri = $santri->kelasPrimary?->id_kelas;

            $query = Berita::where('status', 'published')
                ->where(function($q) use ($idKelasSantri) {
                    $q->where('target_berita', 'semua');

                    if ($idKelasSantri) {
                        $q->orWhere(function($subQ) use ($idKelasSantri) {
                            $subQ->where('target_berita', 'kelas_tertentu')
                                 ->whereJsonContains('target_kelas', $idKelasSantri);
                        });
                    }
                })
                ->select(['id', 'id_berita', 'judul', 'konten', 'penulis', 'gambar', 'target_berita', 'created_at'])
                ->orderBy('created_at', 'desc');

            $berita = $query->paginate(10);

            $data = $berita->map(function($item) {
                return [
                    'id' => $item->id,
                    'id_berita' => $item->id_berita,
                    'judul' => $item->judul,
                    'konten' => $item->konten,
                    'penulis' => $item->penulis,
                    'gambar_url' => $item->gambar ? url('storage/' . $item->gambar) : null,
                    'target_berita' => $item->target_berita,
                    'tanggal' => $item->created_at->format('d M Y'),
                    'tanggal_lengkap' => $item->created_at->format('d F Y, H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $berita->currentPage(),
                    'last_page' => $berita->lastPage(),
                    'total' => $berita->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil berita: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detail berita
     */
    public function show(Request $request, $idBerita)
    {
        try {
            $idSantri = $request->user()->role_id;

            $berita = Berita::where('id_berita', $idBerita)
                ->where('status', 'published')
                ->first();

            if (!$berita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Berita tidak ditemukan',
                ], 404);
            }

            // Cek akses
            $bolehAkses = false;

            if ($berita->target_berita === 'semua') {
                $bolehAkses = true;
            } elseif ($berita->target_berita === 'kelas_tertentu') {
                $santri = Santri::with('kelasPrimary')->where('id_santri', $idSantri)->first();
                $idKelasSantri = $santri?->kelasPrimary?->id_kelas;
                $bolehAkses = $idKelasSantri && in_array($idKelasSantri, $berita->target_kelas ?? []);
            }

            if (!$bolehAkses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke berita ini',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_berita' => $berita->id_berita,
                    'judul' => $berita->judul,
                    'konten' => $berita->konten,
                    'penulis' => $berita->penulis,
                    'gambar_url' => $berita->gambar ? url('storage/' . $berita->gambar) : null,
                    'target_berita' => $berita->target_berita,
                    'tanggal' => $berita->created_at->format('d M Y'),
                    'tanggal_lengkap' => $berita->created_at->format('d F Y, H:i'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail berita: ' . $e->getMessage(),
            ], 500);
        }
    }
}