<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembinaanSanksi;
use Illuminate\Http\Request;

class PembinaanSanksiController extends Controller
{
    public function index()
    {
        $data = PembinaanSanksi::byUrutan()->get();
        
        return view('admin.pembinaan_sanksi.index', compact('data'));
    }

    public function create()
    {
        $last = PembinaanSanksi::orderBy('id', 'desc')->first();
        $nextNum = $last ? intval(substr($last->id_pembinaan, 2)) + 1 : 1;
        $nextId = 'PS' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        
        return view('admin.pembinaan_sanksi.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        PembinaanSanksi::create($validated);

        return redirect()->route('admin.pembinaan-sanksi.index')
            ->with('success', 'Pembinaan & Sanksi berhasil ditambahkan.');
    }

    public function show(PembinaanSanksi $pembinaanSanksi)
    {
        return view('admin.pembinaan_sanksi.show', [
            'pembinaan' => $pembinaanSanksi
        ]);
    }

    public function edit(PembinaanSanksi $pembinaanSanksi)
    {
        return view('admin.pembinaan_sanksi.edit', [
            'pembinaan' => $pembinaanSanksi
        ]);
    }

    public function update(Request $request, PembinaanSanksi $pembinaanSanksi)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $pembinaanSanksi->update($validated);

        return redirect()->route('admin.pembinaan-sanksi.index')
            ->with('success', 'Pembinaan & Sanksi berhasil diperbarui.');
    }

    public function destroy(PembinaanSanksi $pembinaanSanksi)
    {
        $pembinaanSanksi->delete();
        
        return redirect()->route('admin.pembinaan-sanksi.index')
            ->with('success', 'Pembinaan & Sanksi berhasil dihapus.');
    }
}