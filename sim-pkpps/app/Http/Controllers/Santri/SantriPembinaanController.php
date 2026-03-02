<?php
// app/Http/Controllers/Santri/SantriPembinaanController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\PembinaanSanksi;
use Illuminate\Support\Facades\Cache;

class SantriPembinaanController extends Controller
{
    /**
     * Tampilkan daftar konten pembinaan & sanksi
     */
    public function index()
    {
        // Cache 30 menit karena konten jarang berubah
        $pembinaanList = Cache::remember('pembinaan_sanksi_aktif', 1800, function () {
            return PembinaanSanksi::aktif()
                ->byUrutan()
                ->select(['id', 'id_pembinaan', 'judul', 'konten', 'urutan', 'updated_at'])
                ->get();
        });

        return view('santri.pembinaan.index', compact('pembinaanList'));
    }

    /**
     * Tampilkan detail satu konten pembinaan & sanksi
     */
    public function show($id_pembinaan)
    {
        $pembinaan = PembinaanSanksi::aktif()
            ->where('id_pembinaan', $id_pembinaan)
            ->firstOrFail();

        // Konten sebelum dan sesudah untuk navigasi
        $prev = PembinaanSanksi::aktif()
            ->byUrutan()
            ->where('urutan', '<', $pembinaan->urutan)
            ->orWhere(function ($q) use ($pembinaan) {
                $q->where('urutan', $pembinaan->urutan)
                  ->where('id', '<', $pembinaan->id);
            })
            ->orderBy('urutan', 'desc')
            ->first();

        $next = PembinaanSanksi::aktif()
            ->byUrutan()
            ->where('urutan', '>', $pembinaan->urutan)
            ->orWhere(function ($q) use ($pembinaan) {
                $q->where('urutan', $pembinaan->urutan)
                  ->where('id', '>', $pembinaan->id);
            })
            ->orderBy('urutan', 'asc')
            ->first();

        // Semua konten untuk sidebar
        $pembinaanList = Cache::remember('pembinaan_sanksi_aktif', 1800, function () {
            return PembinaanSanksi::aktif()->byUrutan()
                ->select(['id', 'id_pembinaan', 'judul', 'urutan'])
                ->get();
        });

        return view('santri.pembinaan.show', compact(
            'pembinaan',
            'pembinaanList',
            'prev',
            'next'
        ));
    }
}