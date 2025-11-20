<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Tampilkan daftar berita
     */
    public function index(Request $request)
    {
        $query = Berita::query()->with('santriTertentu');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter target
        if ($request->filled('target')) {
            $query->target($request->target);
        }

        $berita = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.berita.index', compact('berita'));
    }

    /**
     * Tampilkan form create
     */
    public function create()
    {
        // Ambil data santri aktif - sesuaikan dengan kolom yang ada di model Santri
        $santri = Santri::aktif()
                       ->select('id_santri', 'nama_lengkap', 'kelas')
                       ->orderBy('nama_lengkap')
                       ->get();
        
        $kelasOptions = ['PB', 'Lambatan', 'Cepatan'];

        return view('admin.berita.create', compact('santri', 'kelasOptions'));
    }

    /**
     * Simpan berita baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'penulis' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'target_berita' => 'required|in:semua,kelas_tertentu,santri_tertentu',
            'target_kelas' => 'nullable|array',
            'target_kelas.*' => 'in:PB,Lambatan,Cepatan',
            'santri_tertentu' => 'nullable|array',
            'santri_tertentu.*' => 'exists:santris,id_santri',
        ], [
            'judul.required' => 'Judul berita wajib diisi',
            'konten.required' => 'Konten berita wajib diisi',
            'penulis.required' => 'Nama penulis wajib diisi',
            'status.required' => 'Status berita wajib dipilih',
            'target_berita.required' => 'Target berita wajib dipilih',
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        // Buat berita
        $berita = Berita::create($validated);

        // Attach santri jika target santri_tertentu
        if ($validated['target_berita'] === 'santri_tertentu' && $request->filled('santri_tertentu')) {
            $berita->santriTertentu()->attach($request->santri_tertentu);
        }

        // Attach santri berdasarkan kelas jika target kelas_tertentu
        if ($validated['target_berita'] === 'kelas_tertentu' && $request->filled('target_kelas')) {
            $santriKelas = Santri::whereIn('kelas', $request->target_kelas)
                                 ->where('status', 'Aktif')
                                 ->pluck('id_santri');
            $berita->santriTertentu()->attach($santriKelas);
        }

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail berita
     */
    public function show(Berita $berita)
    {
        $berita->load('santriTertentu');
        return view('admin.berita.show', compact('berita'));
    }

    /**
     * Tampilkan form edit
     */
    public function edit(Berita $berita)
    {
        $berita->load('santriTertentu');
        
        // Ambil data santri aktif - sesuaikan dengan kolom yang ada di model Santri
        $santri = Santri::aktif()
                       ->select('id_santri', 'nama_lengkap', 'kelas')
                       ->orderBy('nama_lengkap')
                       ->get();
        
        $kelasOptions = ['PB', 'Lambatan', 'Cepatan'];
        
        $selectedSantri = $berita->santriTertentu->pluck('id_santri')->toArray();

        return view('admin.berita.edit', compact('berita', 'santri', 'kelasOptions', 'selectedSantri'));
    }

    /**
     * Update berita
     */
    public function update(Request $request, Berita $berita)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'penulis' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'target_berita' => 'required|in:semua,kelas_tertentu,santri_tertentu',
            'target_kelas' => 'nullable|array',
            'target_kelas.*' => 'in:PB,Lambatan,Cepatan',
            'santri_tertentu' => 'nullable|array',
            'santri_tertentu.*' => 'exists:santris,id_santri',
        ]);

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        // Update berita
        $berita->update($validated);

        // Sync santri
        if ($validated['target_berita'] === 'santri_tertentu' && $request->filled('santri_tertentu')) {
            $berita->santriTertentu()->sync($request->santri_tertentu);
        } elseif ($validated['target_berita'] === 'kelas_tertentu' && $request->filled('target_kelas')) {
            $santriKelas = Santri::whereIn('kelas', $request->target_kelas)
                                 ->where('status', 'Aktif')
                                 ->pluck('id_santri');
            $berita->santriTertentu()->sync($santriKelas);
        } else {
            $berita->santriTertentu()->detach();
        }

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui!');
    }

    /**
     * Hapus berita
     */
    public function destroy(Berita $berita)
    {
        // Hapus gambar jika ada
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil dihapus!');
    }

    /**
     * Tampilkan statistik berita
     */
    public function statistik()
    {
        $totalBerita = Berita::count();
        $totalPublished = Berita::where('status', 'published')->count();
        $totalDraft = Berita::where('status', 'draft')->count();
        $beritaSemua = Berita::where('target_berita', 'semua')->count();
        $beritaTertentu = Berita::where('target_berita', 'santri_tertentu')->count();

        return view('admin.berita.statistik', compact(
            'totalBerita',
            'totalPublished',
            'totalDraft',
            'beritaSemua',
            'beritaTertentu'
        ));
    }
}