<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UangSaku;
use App\Models\Santri;
use Illuminate\Http\Request;

class ApiUangSakuController extends Controller
{
    /**
     * Get saldo uang saku santri berdasarkan token wali
     */
    public function saldo(Request $request)
    {
        try {
            // Ambil id_santri dari user yang login (wali)
            $idSantri = $request->user()->role_id;
            
            // Ambil data santri
            $santri = Santri::where('id_santri', $idSantri)->first();
            
            if (!$santri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan',
                ], 404);
            }
            
            // Query untuk filter
            $query = UangSaku::where('id_santri', $idSantri);
            
            // Filter berdasarkan tanggal
            if ($request->filled('tanggal_dari')) {
                $query->where('tanggal_transaksi', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->where('tanggal_transaksi', '<=', $request->tanggal_sampai);
            }
            
            // Hitung total pemasukan dan pengeluaran sesuai filter
            $totalPemasukan = (clone $query)
                ->where('jenis_transaksi', 'pemasukan')
                ->sum('nominal');
            
            $totalPengeluaran = (clone $query)
                ->where('jenis_transaksi', 'pengeluaran')
                ->sum('nominal');
            
            // Saldo tetap keseluruhan (tidak terfilter)
            $saldo = $santri->saldo_uang_saku;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'saldo' => (int) $saldo,
                    'id_santri' => $santri->id_santri,
                    'nama_santri' => $santri->nama_lengkap,
                    'total_pemasukan' => (int) $totalPemasukan,
                    'total_pengeluaran' => (int) $totalPengeluaran,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil saldo: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get riwayat transaksi uang saku
     */
    public function index(Request $request)
    {
        try {
            // Ambil id_santri dari user yang login (wali)
            $idSantri = $request->user()->role_id;
            
            // Query transaksi uang saku
            $query = UangSaku::where('id_santri', $idSantri)
                ->select([
                    'id',
                    'tanggal_transaksi',
                    'jenis_transaksi',
                    'nominal',
                    'keterangan',
                    'saldo_sebelum',
                    'saldo_sesudah'
                ]);
            
            // Filter berdasarkan jenis transaksi
            if ($request->filled('jenis_transaksi') && $request->jenis_transaksi !== 'semua') {
                $query->where('jenis_transaksi', $request->jenis_transaksi);
            }
            
            // Filter berdasarkan tanggal
            if ($request->filled('tanggal_dari')) {
                $query->where('tanggal_transaksi', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->where('tanggal_transaksi', '<=', $request->tanggal_sampai);
            }
            
            $transaksi = $query->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            // Format data
            $data = $transaksi->map(function($item) {
                return [
                    'id' => $item->id,
                    'tanggal_transaksi' => $item->tanggal_transaksi->format('Y-m-d'),
                    'jenis_transaksi' => $item->jenis_transaksi,
                    'nominal' => (int) $item->nominal,
                    'keterangan' => $item->keterangan,
                    'saldo_sebelum' => (int) $item->saldo_sebelum,
                    'saldo_sesudah' => (int) $item->saldo_sesudah,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $transaksi->currentPage(),
                    'last_page' => $transaksi->lastPage(),
                    'total' => $transaksi->total(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat: ' . $e->getMessage(),
            ], 500);
        }
    }
}