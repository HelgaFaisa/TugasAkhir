<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiKepulanganController;
use App\Http\Controllers\Api\ApiPengajuanKepulanganController;
use App\Http\Controllers\Api\PelanggaranApiController;
use App\Http\Controllers\Api\ApiCapaianController;

/*
|--------------------------------------------------------------------------
| API Routes untuk Mobile App
|--------------------------------------------------------------------------
*/

// Public routes (tanpa auth)
Route::prefix('v1')->group(function () {
    // Login
    Route::post('/login', [ApiAuthController::class, 'login']);
});

// Protected routes (butuh token)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    
    // Profile
    Route::get('/profile', [ApiAuthController::class, 'profile']);
    // Uang Saku
    Route::get('/uang-saku/saldo', [App\Http\Controllers\Api\ApiUangSakuController::class, 'saldo']);
    Route::get('/uang-saku', [App\Http\Controllers\Api\ApiUangSakuController::class, 'index']);
    //  Berita
    Route::get('/berita', [App\Http\Controllers\Api\ApiBeritaController::class, 'index']);
    Route::get('/berita/{id_berita}', [App\Http\Controllers\Api\ApiBeritaController::class, 'show']);
    // Kesehatan
    Route::get('/kesehatan', [App\Http\Controllers\Api\ApiKesehatanController::class, 'index']);
    Route::get('/kesehatan/statistik', [App\Http\Controllers\Api\ApiKesehatanController::class, 'statistik']);
    Route::get('/kesehatan/{id_kesehatan}', [App\Http\Controllers\Api\ApiKesehatanController::class, 'show']);
    // Pembayaran SPP
    Route::get('/spp/status-bulan-ini', [App\Http\Controllers\Api\ApiSppController::class, 'statusBulanIni']);
    Route::get('/spp/tunggakan', [App\Http\Controllers\Api\ApiSppController::class, 'tunggakan']);
    Route::get('/spp/riwayat', [App\Http\Controllers\Api\ApiSppController::class, 'riwayat']);
    Route::get('/spp/statistik', [App\Http\Controllers\Api\ApiSppController::class, 'statistik']);
    // Kepulangan
    Route::get('/kepulangan', [App\Http\Controllers\Api\ApiKepulanganController::class, 'index']);
    Route::get('/kepulangan/kuota', [App\Http\Controllers\Api\ApiKepulanganController::class, 'kuota']);
    Route::get('/kepulangan/notifikasi', [App\Http\Controllers\Api\ApiKepulanganController::class, 'notifikasiKepulangan']);
    Route::get('/kepulangan/{id_kepulangan}', [App\Http\Controllers\Api\ApiKepulanganController::class, 'show']);
    // Pengajuan Kepulangan (Submit dari mobile)
    Route::post('/kepulangan/pengajuan', [ApiPengajuanKepulanganController::class, 'store']);
    Route::get('/kepulangan/pengajuan', [ApiPengajuanKepulanganController::class, 'index']);
    Route::post('/kepulangan/pengajuan/preview', [ApiPengajuanKepulanganController::class, 'preview']);
    // ==========================================
    // PELANGGARAN (BARU) ✅
    // ==========================================
    // Public Info (Untuk semua santri)
    Route::get('/pelanggaran/klasifikasi', [PelanggaranApiController::class, 'getKlasifikasi']);
    Route::get('/pelanggaran/kategori', [PelanggaranApiController::class, 'getKategoriPelanggaran']);
    Route::get('/pelanggaran/pembinaan-sanksi', [PelanggaranApiController::class, 'getPembinaanSanksi']);
    
    // Private - Riwayat Santri (Hanya yang published)
    Route::get('/pelanggaran/riwayat', [PelanggaranApiController::class, 'getRiwayatPelanggaran']);
    Route::get('/pelanggaran/riwayat/{idRiwayat}', [PelanggaranApiController::class, 'getDetailRiwayat']);
    Route::get('/pelanggaran/statistik', [PelanggaranApiController::class, 'getStatistik']);

    // Capaian Overview & Statistik
    Route::get('/capaian/overview', [ApiCapaianController::class, 'overview']);
    // Capaian Dashboard (Comprehensive - for enhanced mobile page)
    Route::get('/capaian/dashboard', [ApiCapaianController::class, 'dashboard']);
    // Trend Semester (progress per semester for line chart)
    Route::get('/capaian/trend-semester', [ApiCapaianController::class, 'trendSemester']);
    // List Materi per Kategori
    Route::get('/capaian/kategori/{kategori}', [ApiCapaianController::class, 'listMateriByKategori']);
    // Detail Capaian
    Route::get('/capaian/detail/{idCapaian}', [ApiCapaianController::class, 'detailCapaian']);
    // Grafik Progress Historis
    Route::get('/capaian/grafik-progress', [ApiCapaianController::class, 'grafikProgress']);

    // ==========================================
    // ABSENSI KEGIATAN (BARU) ✅
    // ==========================================
    Route::prefix('absensi')->group(function () {
        // Dashboard hari ini
        Route::get('/today', [App\Http\Controllers\Api\ApiAbsensiKegiatanController::class, 'today']);
        
        // Summary minggu ini
        Route::get('/week', [App\Http\Controllers\Api\ApiAbsensiKegiatanController::class, 'week']);
        
        // Riwayat bulan
        Route::get('/month', [App\Http\Controllers\Api\ApiAbsensiKegiatanController::class, 'month']);
    });

});