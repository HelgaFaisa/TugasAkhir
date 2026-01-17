<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;

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
    
    // TODO: Tambahkan endpoint lain di sini
    // Route::get('/uang-saku', [ApiUangSakuController::class, 'index']);
    // Route::get('/pelanggaran', [ApiPelanggaranController::class, 'index']);
    // dst...
});