<?php
// app/Http/Controllers/Admin/KategoriPelanggaranController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPelanggaran;
use Illuminate\Http\Request;

class KategoriPelanggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = KategoriPelanggaran::orderBy('created_at', 'desc')->get();
        
        return view('admin.kategori_pelanggaran.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate preview ID kategori berikutnya
        $lastKategori = KategoriPelanggaran::orderBy('id', 'desc')->first();
        $nextNum = $lastKategori ? intval(substr($lastKategori->id_kategori, 2)) + 1 : 1;
        $nextIdKategori = 'KP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        
        return view('admin.kategori_pelanggaran.create', compact('nextIdKategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
        ], [
            'nama_pelanggaran.required' => 'Nama pelanggaran wajib diisi.',
            'poin.required' => 'Poin wajib diisi.',
            'poin.min' => 'Poin minimal 1.',
            'poin.max' => 'Poin maksimal 100.',
        ]);

        KategoriPelanggaran::create($validated);

        return redirect()->route('admin.kategori-pelanggaran.index')
            ->with('success', 'Kategori pelanggaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriPelanggaran $kategoriPelanggaran)
    {
        $kategoriPelanggaran->load('riwayatPelanggaran.santri');
        
        return view('admin.kategori_pelanggaran.show', [
            'kategori' => $kategoriPelanggaran
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriPelanggaran $kategoriPelanggaran)
    {
        return view('admin.kategori_pelanggaran.index', [
            'data' => KategoriPelanggaran::orderBy('created_at', 'desc')->get(),
            'kategori' => $kategoriPelanggaran
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriPelanggaran $kategoriPelanggaran)
    {
        $validated = $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
        ], [
            'nama_pelanggaran.required' => 'Nama pelanggaran wajib diisi.',
            'poin.required' => 'Poin wajib diisi.',
            'poin.min' => 'Poin minimal 1.',
            'poin.max' => 'Poin maksimal 100.',
        ]);

        $kategoriPelanggaran->update($validated);

        return redirect()->route('admin.kategori-pelanggaran.index')
            ->with('success', 'Kategori pelanggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriPelanggaran $kategoriPelanggaran)
    {
        $namaKategori = $kategoriPelanggaran->nama_pelanggaran;
        
        // Cek apakah kategori masih digunakan
        if ($kategoriPelanggaran->riwayatPelanggaran()->count() > 0) {
            return redirect()->route('admin.kategori-pelanggaran.index')
                ->with('error', 'Kategori "' . $namaKategori . '" tidak dapat dihapus karena masih digunakan dalam riwayat pelanggaran.');
        }
        
        $kategoriPelanggaran->delete();
        
        return redirect()->route('admin.kategori-pelanggaran.index')
            ->with('success', 'Kategori "' . $namaKategori . '" berhasil dihapus.');
    }
}