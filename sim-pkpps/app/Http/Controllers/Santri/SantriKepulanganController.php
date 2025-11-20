<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Kepulangan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SantriKepulanganController extends Controller
{
    /**
     * Tampilkan riwayat kepulangan santri yang sedang login
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil data santri
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'nama_lengkap', 'kelas')
            ->firstOrFail();
        
        // Tahun untuk filter
        $tahunSekarang = $request->filled('tahun') ? $request->tahun : Carbon::now()->year;
        
        // Query riwayat kepulangan
        $query = Kepulangan::query()
            ->select([
                'id',
                'id_kepulangan',
                'id_santri',
                'tanggal_izin',
                'tanggal_pulang',
                'tanggal_kembali',
                'durasi_izin',
                'alasan',
                'status',
                'approved_at',
                'created_at'
            ])
            ->where('id_santri', $santri->id_santri)
            ->whereYear('tanggal_pulang', $tahunSekarang);
        
        // Filter status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Urutkan terbaru dan paginate
        $riwayatKepulangan = $query->orderBy('tanggal_pulang', 'desc')
            ->paginate(10)
            ->appends($request->all());
        
        // Hitung statistik tahun ini
        $statistik = [
            'total_izin' => Kepulangan::where('id_santri', $santri->id_santri)
                ->whereYear('tanggal_pulang', $tahunSekarang)
                ->count(),
            'disetujui' => Kepulangan::where('id_santri', $santri->id_santri)
                ->where('status', 'Disetujui')
                ->whereYear('tanggal_pulang', $tahunSekarang)
                ->count(),
            'total_hari' => Kepulangan::where('id_santri', $santri->id_santri)
                ->where('status', 'Disetujui')
                ->whereYear('tanggal_pulang', $tahunSekarang)
                ->sum('durasi_izin'),
            'menunggu' => Kepulangan::where('id_santri', $santri->id_santri)
                ->where('status', 'Menunggu')
                ->whereYear('tanggal_pulang', $tahunSekarang)
                ->count(),
        ];
        
        // Hitung sisa kuota (maksimal 12 hari/tahun)
        $statistik['sisa_kuota'] = max(0, 12 - $statistik['total_hari']);
        $statistik['over_limit'] = $statistik['total_hari'] > 12;
        
        // Data untuk filter
        $statusOptions = [
            'Menunggu' => 'Menunggu Approval',
            'Disetujui' => 'Disetujui',
            'Ditolak' => 'Ditolak',
            'Selesai' => 'Selesai'
        ];
        
        // Tahun options (5 tahun terakhir)
        $tahunOptions = range(Carbon::now()->year, Carbon::now()->year - 4);
        
        return view('santri.kepulangan.index', compact(
            'riwayatKepulangan',
            'santri',
            'statistik',
            'statusOptions',
            'tahunOptions',
            'tahunSekarang'
        ));
    }
    
    /**
     * Tampilkan detail kepulangan
     */
    public function show($id_kepulangan)
    {
        $user = Auth::user();
        
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'nama_lengkap', 'kelas')
            ->firstOrFail();
        
        // Ambil data kepulangan dengan validasi kepemilikan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)
            ->where('id_santri', $santri->id_santri)
            ->firstOrFail();
        
        // Hitung total hari izin tahun ini
        $tahunSekarang = Carbon::now()->year;
        $totalHariTahunIni = Kepulangan::where('id_santri', $santri->id_santri)
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_pulang', $tahunSekarang)
            ->sum('durasi_izin');
        
        $sisaKuota = max(0, 12 - $totalHariTahunIni);
        
        return view('santri.kepulangan.show', compact('kepulangan', 'santri', 'totalHariTahunIni', 'sisaKuota'));
    }
}