<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KlasifikasiPelanggaran;
use Illuminate\Http\Request;

class KlasifikasiPelanggaranController extends Controller
{
    public function index()
    {
        $data = KlasifikasiPelanggaran::withCount('pelanggarans')
            ->byUrutan()
            ->get();
        
        return view('admin.klasifikasi_pelanggaran.index', compact('data'));
    }

    public function create()
    {
        $last = KlasifikasiPelanggaran::orderBy('id', 'desc')->first();
        $nextNum = $last ? intval(substr($last->id_klasifikasi, 2)) + 1 : 1;
        $nextId = 'KL' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        
        return view('admin.klasifikasi_pelanggaran.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_klasifikasi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        KlasifikasiPelanggaran::create($validated);

        return redirect()->route('admin.klasifikasi-pelanggaran.index')
            ->with('success', 'Klasifikasi berhasil ditambahkan.');
    }

    public function show(KlasifikasiPelanggaran $klasifikasiPelanggaran)
    {
        $klasifikasiPelanggaran->load(['pelanggarans' => function($q) {
            $q->aktif()->orderBy('nama_pelanggaran');
        }]);
        
        return view('admin.klasifikasi_pelanggaran.show', [
            'klasifikasi' => $klasifikasiPelanggaran
        ]);
    }

    public function edit(KlasifikasiPelanggaran $klasifikasiPelanggaran)
    {
        return view('admin.klasifikasi_pelanggaran.edit', [
            'klasifikasi' => $klasifikasiPelanggaran
        ]);
    }

    public function update(Request $request, KlasifikasiPelanggaran $klasifikasiPelanggaran)
    {
        $validated = $request->validate([
            'nama_klasifikasi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $klasifikasiPelanggaran->update($validated);

        return redirect()->route('admin.klasifikasi-pelanggaran.index')
            ->with('success', 'Klasifikasi berhasil diperbarui.');
    }

    public function destroy(KlasifikasiPelanggaran $klasifikasiPelanggaran)
    {
        if ($klasifikasiPelanggaran->pelanggarans()->count() > 0) {
            return redirect()->route('admin.klasifikasi-pelanggaran.index')
                ->with('error', 'Klasifikasi tidak dapat dihapus karena masih memiliki pelanggaran.');
        }
        
        $klasifikasiPelanggaran->delete();
        
        return redirect()->route('admin.klasifikasi-pelanggaran.index')
            ->with('success', 'Klasifikasi berhasil dihapus.');
    }
}