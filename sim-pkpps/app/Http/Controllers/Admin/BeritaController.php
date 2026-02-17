<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Tampilkan daftar berita
     */
    public function index(Request $request)
    {
        $query = Berita::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->status($request->status);
        }

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
        $kelasOptions = Kelas::where('is_active', true)->ordered()->get();

        return view('admin.berita.create', compact('kelasOptions'));
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
            'target_berita' => 'required|in:semua,kelas_tertentu',
            'target_kelas' => 'nullable|array',
            'target_kelas.*' => 'exists:kelas,id',
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

        // Konversi target_kelas ke array integer jika kelas_tertentu
        if ($validated['target_berita'] === 'kelas_tertentu' && $request->filled('target_kelas')) {
            $validated['target_kelas'] = array_map('intval', $request->target_kelas);
        } else {
            $validated['target_kelas'] = null;
        }

        Berita::create($validated);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail berita
     */
    public function show(Berita $berita)
    {
        return view('admin.berita.show', compact('berita'));
    }

    /**
     * Tampilkan form edit
     */
    public function edit(Berita $berita)
    {
        $kelasOptions = Kelas::where('is_active', true)->ordered()->get();

        return view('admin.berita.edit', compact('berita', 'kelasOptions'));
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
            'target_berita' => 'required|in:semua,kelas_tertentu',
            'target_kelas' => 'nullable|array',
            'target_kelas.*' => 'exists:kelas,id',
        ]);

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        // Konversi target_kelas
        if ($validated['target_berita'] === 'kelas_tertentu' && $request->filled('target_kelas')) {
            $validated['target_kelas'] = array_map('intval', $request->target_kelas);
        } else {
            $validated['target_kelas'] = null;
        }

        $berita->update($validated);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui!');
    }

    /**
     * Hapus berita
     */
    public function destroy(Berita $berita)
    {
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
        $beritaKelas = Berita::where('target_berita', 'kelas_tertentu')->count();

        return view('admin.berita.statistik', compact(
            'totalBerita',
            'totalPublished',
            'totalDraft',
            'beritaSemua',
            'beritaKelas'
        ));
    }
}
