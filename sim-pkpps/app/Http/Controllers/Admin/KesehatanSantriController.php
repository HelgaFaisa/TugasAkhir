<?php
// app/Http/Controllers/Admin/KesehatanSantriController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KesehatanSantri;
use App\Models\Santri;
use Illuminate\Http\Request;

class KesehatanSantriController extends Controller
{
    /**
     * Tampilkan daftar data kesehatan santri
     */
    public function index(Request $request)
    {
        $query = KesehatanSantri::with('santri');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter bulan
        if ($request->filled('month')) {
            $query->whereMonth('tanggal_masuk', $request->month);
        }

        // Filter tahun
        $year = $request->filled('year') ? $request->year : date('Y');
        $query->whereYear('tanggal_masuk', $year);

        // Urutkan terbaru
        $kesehatanSantri = $query->orderBy('tanggal_masuk', 'desc')->paginate(15);

        // Data untuk filter
        $statusOptions = ['dirawat', 'sembuh', 'izin'];
        $monthOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $yearOptions = range(date('Y'), date('Y') - 5);

        return view('admin.kesehatan-santri.index', compact(
            'kesehatanSantri', 
            'statusOptions', 
            'monthOptions', 
            'yearOptions'
        ));
    }

    /**
     * Tampilkan form tambah data
     */
    public function create()
    {
        // Ambil semua santri aktif
        $santri = Santri::where('status', 'Aktif')
                       ->orderBy('nama_lengkap')
                       ->get();

        return view('admin.kesehatan-santri.create', compact('santri'));
    }

    /**
     * Simpan data kesehatan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'tanggal_masuk' => 'required|date|before_or_equal:today',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk|before_or_equal:today',
            'keluhan' => 'required|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
            'status' => 'required|in:dirawat,sembuh,izin',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists' => 'Santri tidak ditemukan.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh melebihi hari ini.',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar harus setelah tanggal masuk.',
            'keluhan.required' => 'Keluhan wajib diisi.',
            'keluhan.max' => 'Keluhan maksimal 1000 karakter.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Validasi: Jika status bukan dirawat, tanggal keluar wajib diisi
        if ($validated['status'] != 'dirawat' && empty($validated['tanggal_keluar'])) {
            return back()->withErrors([
                'tanggal_keluar' => 'Tanggal keluar wajib diisi untuk status ' . $validated['status']
            ])->withInput();
        }

        // Jika status dirawat, kosongkan tanggal keluar
        if ($validated['status'] == 'dirawat') {
            $validated['tanggal_keluar'] = null;
        }

        KesehatanSantri::create($validated);

        return redirect()->route('admin.kesehatan-santri.index')
                        ->with('success', 'Data kesehatan santri berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail data kesehatan
     */
    public function show(KesehatanSantri $kesehatanSantri)
    {
        // Load relasi santri
        $kesehatanSantri->load('santri');

        // Ambil riwayat kesehatan santri lainnya (5 data terbaru, kecuali data saat ini)
        $riwayatKesehatan = KesehatanSantri::where('id_santri', $kesehatanSantri->id_santri)
                                          ->where('id', '!=', $kesehatanSantri->id)
                                          ->orderBy('tanggal_masuk', 'desc')
                                          ->take(5)
                                          ->get();

        return view('admin.kesehatan-santri.show', compact('kesehatanSantri', 'riwayatKesehatan'));
    }

    /**
     * Tampilkan form edit
     */
    public function edit(KesehatanSantri $kesehatanSantri)
    {
        // Ambil semua santri aktif
        $santri = Santri::where('status', 'Aktif')
                       ->orderBy('nama_lengkap')
                       ->get();

        return view('admin.kesehatan-santri.edit', compact('kesehatanSantri', 'santri'));
    }

    /**
     * Update data kesehatan
     */
    public function update(Request $request, KesehatanSantri $kesehatanSantri)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'tanggal_masuk' => 'required|date|before_or_equal:today',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk|before_or_equal:today',
            'keluhan' => 'required|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
            'status' => 'required|in:dirawat,sembuh,izin',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar harus setelah tanggal masuk.',
            'keluhan.required' => 'Keluhan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Validasi: Jika status bukan dirawat, tanggal keluar wajib diisi
        if ($validated['status'] != 'dirawat' && empty($validated['tanggal_keluar'])) {
            return back()->withErrors([
                'tanggal_keluar' => 'Tanggal keluar wajib diisi untuk status ' . $validated['status']
            ])->withInput();
        }

        // Jika status dirawat, kosongkan tanggal keluar
        if ($validated['status'] == 'dirawat') {
            $validated['tanggal_keluar'] = null;
        }

        $kesehatanSantri->update($validated);

        return redirect()->route('admin.kesehatan-santri.index')
                        ->with('success', 'Data kesehatan santri berhasil diperbarui.');
    }

    /**
     * Hapus data kesehatan
     */
    public function destroy(KesehatanSantri $kesehatanSantri)
    {
        $namaSantri = $kesehatanSantri->santri->nama_lengkap;
        $kesehatanSantri->delete();

        return redirect()->route('admin.kesehatan-santri.index')
                        ->with('success', 'Data kesehatan "' . $namaSantri . '" berhasil dihapus.');
    }

    /**
     * Update status keluar UKP (via AJAX/Modal)
     */
    public function keluarUkp(Request $request, KesehatanSantri $kesehatanSantri)
    {
        $validated = $request->validate([
            'tanggal_keluar' => 'required|date|after_or_equal:' . $kesehatanSantri->tanggal_masuk . '|before_or_equal:today',
            'status' => 'required|in:sembuh,izin',
        ], [
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar harus setelah tanggal masuk.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        $kesehatanSantri->update($validated);

        return redirect()->route('admin.kesehatan-santri.index')
                        ->with('success', 'Santri berhasil keluar dari UKP.');
    }

    /**
     * Tampilkan riwayat kesehatan per santri
     */
    public function riwayat($id_santri)
    {
        // Cari santri
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();

        // Ambil semua riwayat kesehatan santri
        $riwayatKesehatan = KesehatanSantri::where('id_santri', $id_santri)
                                          ->orderBy('tanggal_masuk', 'desc')
                                          ->paginate(15);

        return view('admin.kesehatan-santri.riwayat', compact('santri', 'riwayatKesehatan'));
    }

    /**
     * Cetak surat izin sakit
     */
    public function cetakSurat(KesehatanSantri $kesehatanSantri)
    {
        $kesehatanSantri->load('santri');
        
        return view('admin.kesehatan-santri.cetak-surat', compact('kesehatanSantri'));
    }
}