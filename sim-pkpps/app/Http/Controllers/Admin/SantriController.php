<?php
// app/Http/Controllers/Admin/SantriController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\KelompokKelas;
use App\Models\SantriKelas;
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
        $query = Santri::with(['kelasSantri.kelas.kelompok']);

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

        // Filter berdasarkan kelas spesifik
        if ($request->filled('id_kelas')) {
            $query->whereHas('kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
        }

        // Select kolom yang diperlukan saja
        $santris = $query->select(
                'id', 
                'id_santri', 
                'nis', 
                'nama_lengkap', 
                'jenis_kelamin', 
                'status',
                'foto',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        // Load kelompok kelas untuk filter dropdown
        $kelompokKelas = KelompokKelas::with(['kelas' => function($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])->active()->ordered()->get();

        return view('admin.santri.index', compact('santris', 'kelompokKelas'));
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

        // Load kelompok kelas untuk dropdown bertingkat
        $kelompokKelas = KelompokKelas::with(['kelas' => function($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])->active()->ordered()->get();
        
        return view('admin.santri.create', compact('nextIdSantri', 'kelompokKelas'));
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
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'nullable|array',
            'kelas_ids.*.*' => 'exists:kelas,id',
            'status' => 'required|in:Aktif,Lulus,Tidak Aktif',
            'alamat_santri' => 'nullable|string',
            'daerah_asal' => 'nullable|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nomor_hp_ortu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nis.unique' => 'NIS sudah digunakan oleh santri lain.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Flatten nested array: kelas_ids[kelompok][] → flat array of kelas IDs
        $kelasIdsFlat = [];
        if (isset($validated['kelas_ids']) && is_array($validated['kelas_ids'])) {
            foreach ($validated['kelas_ids'] as $kelompok => $kelasArray) {
                if (is_array($kelasArray)) {
                    $kelasIdsFlat = array_merge($kelasIdsFlat, $kelasArray);
                }
            }
        }
        
        // Validasi minimal 1 kelas dipilih
        if (empty($kelasIdsFlat)) {
            return back()->withInput()->withErrors(['kelas_ids' => 'Minimal satu kelas wajib dipilih.']);
        }

        // Hapus kelas_ids dari validated (bukan kolom santri)
        unset($validated['kelas_ids']);

        // Buat santri
        $santri = Santri::create($validated);

        // Assign semua kelas yang dipilih
        $tahunAjaran = SantriKelas::getCurrentAcademicYear();
        $isFirst = true;
        foreach ($kelasIdsFlat as $idKelas) {
            $santri->assignKelas($idKelas, $tahunAjaran, $isFirst);
            $isFirst = false;
        }

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
        $santri->load('kelasSantri.kelas.kelompok');
        return view('admin.santri.show', compact('santri'));
    }

    /**
     * Tampilkan form untuk mengedit santri.
     */
    public function edit(Santri $santri)
    {
        $santri->load('kelasSantri.kelas.kelompok');

        // Load kelompok kelas untuk dropdown bertingkat
        $kelompokKelas = KelompokKelas::with(['kelas' => function($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])->active()->ordered()->get();

        return view('admin.santri.edit', compact('santri', 'kelompokKelas'));
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
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'nullable|array',
            'kelas_ids.*.*' => 'exists:kelas,id',
            'status' => 'required|in:Aktif,Lulus,Tidak Aktif',
            'alamat_santri' => 'nullable|string',
            'daerah_asal' => 'nullable|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nomor_hp_ortu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nis.unique' => 'NIS sudah digunakan oleh santri lain.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Flatten nested array: kelas_ids[kelompok][] → flat array of kelas IDs
        $kelasIdsFlat = [];
        if (isset($validated['kelas_ids']) && is_array($validated['kelas_ids'])) {
            foreach ($validated['kelas_ids'] as $kelompok => $kelasArray) {
                if (is_array($kelasArray)) {
                    $kelasIdsFlat = array_merge($kelasIdsFlat, $kelasArray);
                }
            }
        }
        
        // Validasi minimal 1 kelas dipilih
        if (empty($kelasIdsFlat)) {
            return back()->withInput()->withErrors(['kelas_ids' => 'Minimal satu kelas wajib dipilih.']);
        }

        // Hapus kelas_ids dari validated (bukan kolom santri)
        unset($validated['kelas_ids']);

        // Handle upload foto baru
        if ($request->hasFile('foto')) {
            if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                Storage::disk('public')->delete($santri->foto);
            }
            
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename = $santri->id_santri . '.' . $extension;
            $path = $file->storeAs('santri', $filename, 'public');
            $validated['foto'] = $path;
        }

        $santri->update($validated);

        // Sync kelas: hapus semua kelas tahun ini, lalu assign ulang
        $tahunAjaran = SantriKelas::getCurrentAcademicYear();
        $santri->kelasSantri()
               ->where('tahun_ajaran', $tahunAjaran)
               ->delete();

        $isFirst = true;
        foreach ($kelasIdsFlat as $idKelas) {
            $santri->assignKelas($idKelas, $tahunAjaran, $isFirst);
            $isFirst = false;
        }

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