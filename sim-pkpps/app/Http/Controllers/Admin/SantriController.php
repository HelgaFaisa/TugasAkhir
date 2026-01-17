<?php
// app/Http/Controllers/Admin/SantriController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SantriController extends Controller
{
    /**
     * Tampilkan daftar data santri dengan fitur search.
     */
    public function index(Request $request)
    {
        $query = Santri::query();

        // Search berdasarkan nama, NIS, atau ID Santri
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('id_santri', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Select kolom yang diperlukan saja
        $santris = $query->select(
                'id', 
                'id_santri', 
                'nis', 
                'nama_lengkap', 
                'jenis_kelamin', 
                'kelas', 
                'status',
                'foto', // TAMBAHAN
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        return view('admin.santri.index', compact('santris'));
    }

    /**
     * Tampilkan form untuk membuat santri baru.
     */
    public function create()
    {
        // Cache last santri ID selama 1 menit
        $nextIdSantri = Cache::remember('next_santri_id', 60, function () {
            $lastSantri = Santri::select('id_santri')
                ->orderBy('id', 'desc')
                ->first();
            $nextNum = $lastSantri ? intval(substr($lastSantri->id_santri, 1)) + 1 : 1;
            return 'S' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        });
        
        return view('admin.santri.create', compact('nextIdSantri'));
    }

    /**
     * Simpan santri baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:255|unique:santris,nis',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kelas' => 'required|in:PB,Lambatan,Cepatan',
            'status' => 'required|in:Aktif,Lulus,Tidak Aktif',
            'alamat_santri' => 'nullable|string',
            'daerah_asal' => 'nullable|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nomor_hp_ortu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // TAMBAHAN: max 2MB
        ], [
            'nis.unique' => 'NIS sudah digunakan oleh santri lain.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Buat santri terlebih dahulu untuk mendapatkan id_santri
        $santri = Santri::create($validated);

        // Handle upload foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename = $santri->id_santri . '.' . $extension;
            
            // Simpan file ke storage/app/public/santri
            $path = $file->storeAs('santri', $filename, 'public');
            
            // Update path foto di database
            $santri->update(['foto' => $path]);
        }

        // Clear cache
        Cache::forget('next_santri_id');
        Cache::forget('santris_tanpa_akun');
        Cache::forget('santri_aktif_list');

        return redirect()->route('admin.santri.index')
            ->with('success', 'Data santri berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail santri.
     */
    public function show(Santri $santri)
    {
        return view('admin.santri.show', compact('santri'));
    }

    /**
     * Tampilkan form untuk mengedit santri.
     */
    public function edit(Santri $santri)
    {
        return view('admin.santri.edit', compact('santri'));
    }

    /**
     * Update data santri di database.
     */
    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:255|unique:santris,nis,' . $santri->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kelas' => 'required|in:PB,Lambatan,Cepatan',
            'status' => 'required|in:Aktif,Lulus,Tidak Aktif',
            'alamat_santri' => 'nullable|string',
            'daerah_asal' => 'nullable|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nomor_hp_ortu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // TAMBAHAN: max 2MB
        ], [
            'nis.unique' => 'NIS sudah digunakan oleh santri lain.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Handle upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                Storage::disk('public')->delete($santri->foto);
            }
            
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename = $santri->id_santri . '.' . $extension;
            
            // Simpan file ke storage/app/public/santri
            $path = $file->storeAs('santri', $filename, 'public');
            $validated['foto'] = $path;
        }

        $santri->update($validated);

        // Clear cache
        Cache::forget('santris_tanpa_akun');
        Cache::forget('santri_aktif_list');

        return redirect()->route('admin.santri.index')
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    /**
     * Hapus data santri dari database.
     */
    public function destroy(Santri $santri)
    {
        $namaSantri = $santri->nama_lengkap;
        
        // Hapus foto jika ada
        if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
            Storage::disk('public')->delete($santri->foto);
        }
        
        $santri->delete();
        
        // Clear cache
        Cache::forget('santris_tanpa_akun');
        Cache::forget('santri_aktif_list');
        
        return redirect()->route('admin.santri.index')
            ->with('success', 'Data santri "' . $namaSantri . '" berhasil dihapus.');
    }
}