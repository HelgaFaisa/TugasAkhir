<?php
// app/Http/Controllers/Santri/SantriProfileController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Support\Facades\Cache;

class SantriProfileController extends Controller
{
    private function getSantriId()
    {
        return auth('santri')->user()->id_santri;
    }

    /**
     * Tampilkan halaman profil santri yang sedang login (READ ONLY)
     */
    public function index()
    {
        $idSantri = $this->getSantriId();

        $santri = Cache::remember(
            'santri_profile_' . $idSantri,
            600,
            function () use ($idSantri) {
                return Santri::with(['kelasSantri.kelas.kelompok', 'kelasPrimary.kelas.kelompok'])
                    ->where('id_santri', $idSantri)
                    ->select([
                        'id',
                        'id_santri',
                        'nis',
                        'nama_lengkap',
                        'jenis_kelamin',
                        'status',
                        'alamat_santri',
                        'daerah_asal',
                        'nama_orang_tua',
                        'nomor_hp_ortu',
                        'rfid_uid',
                        'foto',
                        'created_at',
                        'updated_at',
                    ])
                    ->firstOrFail();
            }
        );

        return view('santri.profil.index', compact('santri'));
    }
}