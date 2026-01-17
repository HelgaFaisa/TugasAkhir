<?php
// app/Http/Controllers/Santri/SantriProfileController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SantriProfileController extends Controller
{
    /**
     * Tampilkan halaman profil santri yang sedang login
     */
    public function index()
    {
        // Ambil data user yang sedang login
        $user = Auth::guard('web')->user();
        
        // Pastikan user adalah santri
        if ($user->role !== 'santri') {
            abort(403, 'Unauthorized access');
        }
        
        // Cache data santri selama 10 menit untuk mengurangi query database
        $santri = Cache::remember(
            'santri_profile_' . $user->role_id, 
            600, // 10 menit
            function () use ($user) {
                return Santri::where('id_santri', $user->role_id)
                    ->select([
                        'id',
                        'id_santri',
                        'nis',
                        'nama_lengkap',
                        'jenis_kelamin',
                        'kelas',
                        'status',
                        'alamat_santri',
                        'daerah_asal',
                        'nama_orang_tua',
                        'nomor_hp_ortu',
                        'rfid_uid',
                        'foto', // ✅ TAMBAHAN INI - PENTING!
                        'created_at'
                    ])
                    ->firstOrFail();
            }
        );
        
        return view('santri.profil.index', compact('santri'));
    }
    
    /**
     * Tampilkan form edit profil (data terbatas yang bisa diedit santri)
     */
    public function edit()
    {
        $user = Auth::guard('web')->user();
        
        if ($user->role !== 'santri') {
            abort(403, 'Unauthorized access');
        }
        
        $santri = Santri::where('id_santri', $user->role_id)
            ->select([
                'id',
                'id_santri',
                'nama_lengkap',
                'jenis_kelamin', // ✅ TAMBAHAN untuk fallback foto default
                'alamat_santri',
                'nomor_hp_ortu',
                'foto' // ✅ TAMBAHAN INI - PENTING!
            ])
            ->firstOrFail();
        
        return view('santri.profil.edit', compact('santri'));
    }
    
    /**
     * Update profil santri (hanya field tertentu yang boleh diedit)
     */
    public function update(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        if ($user->role !== 'santri') {
            abort(403, 'Unauthorized access');
        }
        
        $validated = $request->validate([
            'alamat_santri' => 'nullable|string|max:500',
            'nomor_hp_ortu' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
        ], [
            'nomor_hp_ortu.regex' => 'Format nomor HP tidak valid. Hanya boleh berisi angka, +, -, spasi, dan tanda kurung.',
            'alamat_santri.max' => 'Alamat maksimal 500 karakter.',
        ]);
        
        $santri = Santri::where('id_santri', $user->role_id)->firstOrFail();
        $santri->update($validated);
        
        // Clear cache setelah update
        Cache::forget('santri_profile_' . $user->role_id);
        
        return redirect()->route('santri.profil.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}