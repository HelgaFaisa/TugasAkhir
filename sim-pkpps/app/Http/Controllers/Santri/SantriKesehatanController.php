<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\KesehatanSantri;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SantriKesehatanController extends Controller
{
    /**
     * Tampilkan riwayat kesehatan santri yang sedang login dengan filter tanggal
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil data santri
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'nama_lengkap', 'kelas')
            ->firstOrFail();
        
        // ✅ TENTUKAN RANGE TANGGAL
        // Jika tidak ada filter, default bulan ini
        $tanggalDari = $request->filled('tanggal_dari') 
            ? Carbon::parse($request->tanggal_dari) 
            : Carbon::now()->startOfMonth();
        
        $tanggalSampai = $request->filled('tanggal_sampai') 
            ? Carbon::parse($request->tanggal_sampai) 
            : Carbon::now()->endOfMonth();
        
        // Validasi: tanggal_sampai tidak boleh lebih kecil dari tanggal_dari
        if ($tanggalSampai->lt($tanggalDari)) {
            return back()->withErrors([
                'tanggal_sampai' => 'Tanggal sampai harus lebih besar atau sama dengan tanggal dari.'
            ])->withInput();
        }
        
        // ✅ QUERY DASAR DENGAN FILTER TANGGAL
        $baseQuery = KesehatanSantri::where('id_santri', $santri->id_santri)
            ->whereBetween('tanggal_masuk', [
                $tanggalDari->format('Y-m-d'),
                $tanggalSampai->format('Y-m-d')
            ]);
        
        // ✅ HITUNG STATISTIK BERDASARKAN FILTER TANGGAL
        $statistik = [
            'total_kunjungan' => (clone $baseQuery)->count(),
            'sedang_dirawat' => (clone $baseQuery)->where('status', 'dirawat')->count(),
            'sembuh' => (clone $baseQuery)->where('status', 'sembuh')->count(),
            'izin' => (clone $baseQuery)->where('status', 'izin')->count(),
        ];
        
        // ✅ QUERY RIWAYAT KESEHATAN UNTUK TABEL
        $query = KesehatanSantri::query()
            ->select([
                'id',
                'id_kesehatan',
                'id_santri',
                'tanggal_masuk',
                'tanggal_keluar',
                'keluhan',
                'status',
                'created_at'
            ])
            ->where('id_santri', $santri->id_santri)
            ->whereBetween('tanggal_masuk', [
                $tanggalDari->format('Y-m-d'),
                $tanggalSampai->format('Y-m-d')
            ]);
        
        // Filter status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Urutkan terbaru dan paginate
        $riwayatKesehatan = $query->orderBy('tanggal_masuk', 'desc')
            ->paginate(10)
            ->appends($request->all()); // Append query string untuk pagination
        
        // Data untuk filter
        $statusOptions = [
            'dirawat' => 'Sedang Dirawat',
            'sembuh' => 'Sembuh',
            'izin' => 'Izin Sakit'
        ];
        
        return view('santri.kesehatan.index', compact(
            'riwayatKesehatan',
            'santri',
            'statistik',
            'statusOptions',
            'tanggalDari',
            'tanggalSampai'
        ));
    }
    
    /**
     * Tampilkan detail riwayat kesehatan
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'nama_lengkap', 'kelas')
            ->firstOrFail();
        
        // Ambil data kesehatan dengan validasi kepemilikan
        $kesehatanSantri = KesehatanSantri::where('id', $id)
            ->where('id_santri', $santri->id_santri)
            ->firstOrFail();
        
        return view('santri.kesehatan.show', compact('kesehatanSantri', 'santri'));
    }
}