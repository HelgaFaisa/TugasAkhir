<?php
// app/Http/Controllers/admin/KategoriKegiatanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KategoriKegiatanController extends Controller
{
    /**
     * Tampilkan daftar kategori kegiatan
     */
    public function index(Request $request)
    {
        $query = KategoriKegiatan::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('kategori_id', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $kategoris = $query->select('id', 'kategori_id', 'nama_kategori', 'keterangan', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        return view('admin.kegiatan.kategori.index', compact('kategoris'));
    }

    /**
     * Form tambah kategori
     */
    public function create()
    {
        // Preview ID berikutnya
        $nextId = Cache::remember('next_kategori_id', 60, function () {
            $last = KategoriKegiatan::select('kategori_id')->orderBy('id', 'desc')->first();
            $num = $last ? intval(substr($last->kategori_id, 2)) + 1 : 1;
            return 'KT' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });
        
        return view('admin.kegiatan.kategori.create', compact('nextId'));
    }

    /**
     * Simpan kategori baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_kegiatans,nama_kategori',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        KategoriKegiatan::create($validated);
        Cache::forget('next_kategori_id');

        return redirect()->route('admin.kategori-kegiatan.index')
            ->with('success', 'Kategori kegiatan berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail kategori
     */
    public function show(KategoriKegiatan $kategoriKegiatan)
    {
        return view('admin.kegiatan.kategori.show', compact('kategoriKegiatan'));
    }

    /**
     * Form edit kategori
     */
    public function edit(KategoriKegiatan $kategoriKegiatan)
    {
        return view('admin.kegiatan.kategori.edit', compact('kategoriKegiatan'));
    }

    /**
     * Update kategori
     */
    public function update(Request $request, KategoriKegiatan $kategoriKegiatan)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_kegiatans,nama_kategori,' . $kategoriKegiatan->id,
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $kategoriKegiatan->update($validated);

        return redirect()->route('admin.kategori-kegiatan.index')
            ->with('success', 'Kategori kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus kategori
     */
    public function destroy(KategoriKegiatan $kategoriKegiatan)
    {
        $nama = $kategoriKegiatan->nama_kategori;
        $kategoriKegiatan->delete();
        Cache::forget('next_kategori_id');

        return redirect()->route('admin.kategori-kegiatan.index')
            ->with('success', "Kategori \"$nama\" berhasil dihapus.");
    }
}