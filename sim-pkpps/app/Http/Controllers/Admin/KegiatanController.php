<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\KategoriKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KegiatanController extends Controller
{
    /**
     * Tampilkan daftar kegiatan
     */
    public function index(Request $request)
    {
        $query = Kegiatan::with('kategori');

        // Filter hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $kegiatans = $query->select('id', 'kegiatan_id', 'kategori_id', 'nama_kegiatan', 'hari', 'waktu_mulai', 'waktu_selesai', 'materi')
            ->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->paginate(15)
            ->appends(request()->query());

        // Data untuk filter
        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];

        return view('admin.kegiatan.data.index', compact('kegiatans', 'kategoris', 'hariList'));
    }

    /**
     * Form tambah kegiatan
     */
    public function create()
    {
        $nextId = Cache::remember('next_kegiatan_id', 60, function () {
            $last = Kegiatan::select('kegiatan_id')->orderBy('id', 'desc')->first();
            $num = $last ? intval(substr($last->kegiatan_id, 2)) + 1 : 1;
            return 'KG' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });

        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];

        return view('admin.kegiatan.data.create', compact('nextId', 'kategoris', 'hariList'));
    }

    /**
     * Simpan kegiatan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_kegiatans,kategori_id',
            'nama_kegiatan' => 'required|string|max:150',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'materi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'hari.required' => 'Hari wajib dipilih.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus lebih dari waktu mulai.',
        ]);

        Kegiatan::create($validated);
        Cache::forget('next_kegiatan_id');

        return redirect()->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail kegiatan
     */
    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load('kategori');
        return view('admin.kegiatan.data.show', compact('kegiatan'));
    }

    /**
     * Form edit kegiatan
     */
    public function edit(Kegiatan $kegiatan)
    {
        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];

        return view('admin.kegiatan.data.edit', compact('kegiatan', 'kategoris', 'hariList'));
    }

    /**
     * Update kegiatan
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_kegiatans,kategori_id',
            'nama_kegiatan' => 'required|string|max:150',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'materi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus lebih dari waktu mulai.',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus kegiatan
     */
    public function destroy(Kegiatan $kegiatan)
    {
        $nama = $kegiatan->nama_kegiatan;
        $kegiatan->delete();
        Cache::forget('next_kegiatan_id');

        return redirect()->route('admin.kegiatan.index')
            ->with('success', "Kegiatan \"$nama\" berhasil dihapus.");
    }
}