<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPelanggaran;
use App\Models\KlasifikasiPelanggaran;
use Illuminate\Http\Request;

class KategoriPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = KategoriPelanggaran::with('klasifikasi');

        // Filter klasifikasi
        if ($request->filled('id_klasifikasi')) {
            $query->byKlasifikasi($request->id_klasifikasi);
        }

        // Filter status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        $klasifikasiList = KlasifikasiPelanggaran::aktif()->byUrutan()->get();
        
        return view('admin.kategori_pelanggaran.index', compact('data', 'klasifikasiList'));
    }

    public function create()
    {
        $last = KategoriPelanggaran::orderBy('id', 'desc')->first();
        $nextNum = $last ? intval(substr($last->id_kategori, 2)) + 1 : 1;
        $nextId = 'KP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        
        $klasifikasiList = KlasifikasiPelanggaran::aktif()->byUrutan()->get();
        
        return view('admin.kategori_pelanggaran.create', compact('nextId', 'klasifikasiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_klasifikasi' => 'required|exists:klasifikasi_pelanggarans,id_klasifikasi',
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
            'kafaroh' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        KategoriPelanggaran::create($validated);

        return redirect()->route('admin.kategori-pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil ditambahkan.');
    }

    public function show(KategoriPelanggaran $kategoriPelanggaran)
    {
        $kategoriPelanggaran->load(['klasifikasi', 'riwayatPelanggaran.santri']);
        
        return view('admin.kategori_pelanggaran.show', [
            'kategori' => $kategoriPelanggaran
        ]);
    }

    public function edit(KategoriPelanggaran $kategoriPelanggaran)
    {
        $klasifikasiList = KlasifikasiPelanggaran::aktif()->byUrutan()->get();
        
        return view('admin.kategori_pelanggaran.edit', [
            'kategori' => $kategoriPelanggaran,
            'klasifikasiList' => $klasifikasiList
        ]);
    }

    public function update(Request $request, KategoriPelanggaran $kategoriPelanggaran)
    {
        $validated = $request->validate([
            'id_klasifikasi' => 'required|exists:klasifikasi_pelanggarans,id_klasifikasi',
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
            'kafaroh' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $kategoriPelanggaran->update($validated);

        return redirect()->route('admin.kategori-pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil diperbarui.');
    }

    public function destroy(KategoriPelanggaran $kategoriPelanggaran)
    {
        if ($kategoriPelanggaran->riwayatPelanggaran()->count() > 0) {
            return redirect()->route('admin.kategori-pelanggaran.index')
                ->with('error', 'Pelanggaran tidak dapat dihapus karena masih digunakan.');
        }
        
        $kategoriPelanggaran->delete();
        
        return redirect()->route('admin.kategori-pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil dihapus.');
    }
}