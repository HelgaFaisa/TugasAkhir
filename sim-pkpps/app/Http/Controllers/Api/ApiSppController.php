<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembayaranSpp;
use App\Models\Santri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiSppController extends Controller
{
    /**
     * Get status SPP bulan berjalan
     */
    public function statusBulanIni(Request $request)
    {
        try {
            $idSantri = $request->user()->role_id;
            $bulanIni = date('n');
            $tahunIni = date('Y');
            
            // Cari SPP bulan ini
            $spp = PembayaranSpp::where('id_santri', $idSantri)
                ->where('bulan', $bulanIni)
                ->where('tahun', $tahunIni)
                ->first();
            
            if (!$spp) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'ada_tagihan' => false,
                        'status' => 'Belum Ada Tagihan',
                        'periode' => $this->getNamaBulan($bulanIni) . ' ' . $tahunIni,
                    ]
                ], 200);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'ada_tagihan' => true,
                    'id_pembayaran' => $spp->id_pembayaran,
                    'periode' => $this->getNamaBulan($spp->bulan) . ' ' . $spp->tahun,
                    'nominal' => (int) $spp->nominal,
                    'status' => $spp->status,
                    'tanggal_bayar' => $spp->tanggal_bayar ? $spp->tanggal_bayar->format('Y-m-d') : null,
                    'tanggal_bayar_formatted' => $spp->tanggal_bayar ? $spp->tanggal_bayar->format('d M Y') : null,
                    'batas_bayar' => $spp->batas_bayar->format('Y-m-d'),
                    'batas_bayar_formatted' => $spp->batas_bayar->format('d M Y'),
                    'is_telat' => $spp->isTelat(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status SPP: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get info tunggakan
     */
    public function tunggakan(Request $request)
    {
        try {
            $idSantri = $request->user()->role_id;
            
            // Hitung tunggakan
            $tunggakanList = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Belum Lunas')
                ->orderBy('tahun', 'asc')
                ->orderBy('bulan', 'asc')
                ->get();
            
            $totalTunggakan = $tunggakanList->sum('nominal');
            $jumlahBulan = $tunggakanList->count();
            $adaTelat = $tunggakanList->filter(fn($spp) => $spp->isTelat())->count() > 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'ada_tunggakan' => $jumlahBulan > 0,
                    'total_tunggakan' => (int) $totalTunggakan,
                    'jumlah_bulan' => $jumlahBulan,
                    'ada_telat' => $adaTelat,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tunggakan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get riwayat pembayaran SPP
     */
    public function riwayat(Request $request)
    {
        try {
            $idSantri = $request->user()->role_id;
            
            // Query riwayat
            $query = PembayaranSpp::where('id_santri', $idSantri)
                ->select([
                    'id',
                    'id_pembayaran',
                    'bulan',
                    'tahun',
                    'nominal',
                    'status',
                    'tanggal_bayar',
                    'batas_bayar',
                    'keterangan'
                ])
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc');
            
            // Filter status (optional)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Pagination
            $riwayat = $query->paginate(20);
            
            // Format data
            $data = $riwayat->map(function($item) {
                return [
                    'id' => $item->id,
                    'id_pembayaran' => $item->id_pembayaran,
                    'periode' => $this->getNamaBulan($item->bulan) . ' ' . $item->tahun,
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'bulan_nama' => $this->getNamaBulan($item->bulan),
                    'nominal' => (int) $item->nominal,
                    'status' => $item->status,
                    'tanggal_bayar' => $item->tanggal_bayar ? $item->tanggal_bayar->format('Y-m-d') : null,
                    'tanggal_bayar_formatted' => $item->tanggal_bayar ? $item->tanggal_bayar->format('d M Y') : null,
                    'batas_bayar' => $item->batas_bayar->format('Y-m-d'),
                    'batas_bayar_formatted' => $item->batas_bayar->format('d M Y'),
                    'is_telat' => $item->isTelat(),
                    'keterangan' => $item->keterangan,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $riwayat->currentPage(),
                    'last_page' => $riwayat->lastPage(),
                    'total' => $riwayat->total(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get statistik pembayaran SPP
     */
    public function statistik(Request $request)
    {
        try {
            $idSantri = $request->user()->role_id;
            
            $totalLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Lunas')
                ->count();
            
            $totalBelumLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Belum Lunas')
                ->count();
            
            $totalNominalLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Lunas')
                ->sum('nominal');
            
            $totalNominalBelumLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Belum Lunas')
                ->sum('nominal');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_lunas' => $totalLunas,
                    'total_belum_lunas' => $totalBelumLunas,
                    'total_nominal_lunas' => (int) $totalNominalLunas,
                    'total_nominal_belum_lunas' => (int) $totalNominalBelumLunas,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Helper: Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $namaBulan[$bulan] ?? '';
    }
}