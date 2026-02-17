<?php
// routes/web.php (Complete Routes - FIXED VERSION)

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SantriController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KesehatanSantriController;
use App\Http\Controllers\Admin\KepulanganController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\KategoriPelanggaranController;
use App\Http\Controllers\Admin\RiwayatPelanggaranController;
use App\Http\Controllers\Admin\KlasifikasiPelanggaranController; // Tambahkan ini
use App\Http\Controllers\Admin\PembinaanSanksiController; // Tambahkan ini
use App\Http\Controllers\Admin\PembayaranSppController;
use App\Http\Controllers\Admin\UangSakuController;
use App\Http\Controllers\Admin\KategoriKegiatanController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\AbsensiKegiatanController;
use App\Http\Controllers\Admin\KartuRfidController;
use App\Http\Controllers\Admin\RiwayatKegiatanController;
use App\Http\Controllers\Admin\LaporanKegiatanController;
use App\Http\Controllers\Admin\MateriController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\CapaianController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\SantriAuthController;
use App\Http\Controllers\Santri\SantriProfileController; // BARU
use App\Http\Controllers\Santri\SantriUangSakuController;
use App\Http\Controllers\Santri\SantriPelanggaranController;
use App\Http\Controllers\Santri\SantriBeritaController; // ✅ TAMBAHKAN INI
use App\Http\Controllers\Santri\SantriKesehatanController;
use App\Http\Controllers\Santri\SantriCapaianController;
use App\Http\Controllers\Santri\SantriKepulanganController;
use App\Http\Controllers\Santri\RiwayatKegiatanSantriController; // ✅ TAMBAHKAN INI

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route fallback 'login' untuk middleware authenticate
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Halaman utama (root /) akan dialihkan ke halaman login admin
Route::get('/', function () {
    return redirect()->route('admin.login');
})->name('home');

// --- RUTE OTENTIKASI ADMIN ---
Route::prefix('admin')->middleware('guest')->group(function () {
    // Login
    Route::get('login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'authenticate']);
    
    // Register (Hanya untuk Admin)
    Route::get('register', [AdminAuthController::class, 'register'])->name('admin.register');
    Route::post('register', [AdminAuthController::class, 'storeRegister']); 
});

// --- RUTE OTENTIKASI SANTRI/WALI ---
Route::prefix('santri')->middleware('guest')->group(function () {
    // Login
    Route::get('login', [SantriAuthController::class, 'login'])->name('santri.login');
    Route::post('login', [SantriAuthController::class, 'authenticate']);
});

// --- RUTE ADMINISTRATOR (Prefix: admin) ---
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
    
    // Logout Admin
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // 1. Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // 2. Data Santri (CRUD)
    Route::resource('santri', SantriController::class);

    // 3. Manajemen Pengguna (Akun Santri & Wali)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('santri', [UserController::class, 'santriAccounts'])->name('santri_accounts');
        Route::get('santri/create', [UserController::class, 'createAccount'])->defaults('role', 'santri')->name('santri_create');
        Route::post('santri/store', [UserController::class, 'storeAccount'])->defaults('role', 'santri')->name('santri_store');
        Route::post('santri/{userId}/delete', [UserController::class, 'destroyAccount'])->defaults('role', 'santri')->name('santri_destroy');
        Route::post('santri/{userId}/reset-password', [UserController::class, 'resetPassword'])->defaults('role', 'santri')->name('santri_reset_password');

        Route::get('wali', [UserController::class, 'waliAccounts'])->name('wali_accounts');
        Route::get('wali/create', [UserController::class, 'createAccount'])->defaults('role', 'wali')->name('wali_create');
        Route::post('wali/store', [UserController::class, 'storeAccount'])->defaults('role', 'wali')->name('wali_store');
        Route::post('wali/{userId}/delete', [UserController::class, 'destroyAccount'])->defaults('role', 'wali')->name('wali_destroy');
        Route::post('wali/{userId}/reset-password', [UserController::class, 'resetPassword'])->defaults('role', 'wali')->name('wali_reset_password');
    });
    
    // 4. Kesehatan Santri
    Route::resource('kesehatan-santri', KesehatanSantriController::class);
    
    // Route tambahan untuk keluar UKP
    Route::patch('kesehatan-santri/{kesehatanSantri}/keluar-ukp', [
        KesehatanSantriController::class, 
        'keluarUkp'
    ])->name('kesehatan-santri.keluar-ukp');
    
    // Route untuk riwayat kesehatan per santri
    Route::get('kesehatan-santri/riwayat/{id_santri}', [
        KesehatanSantriController::class, 
        'riwayat'
    ])->name('kesehatan-santri.riwayat');
    
    // Route untuk cetak surat
    Route::get('kesehatan-santri/{kesehatanSantri}/cetak-surat', [
        KesehatanSantriController::class, 
        'cetakSurat'
    ])->name('kesehatan-santri.cetak-surat');
    
    // 5. KEPULANGAN SANTRI (UPDATED - LENGKAP) ✅
    Route::prefix('kepulangan')->name('kepulangan.')->group(function () {
        
        // PENGAJUAN MOBILE (HARUS DI ATAS ROUTE PARAMETER)
        Route::get('/pengajuan', [KepulanganController::class, 'pengajuan'])->name('pengajuan');
        Route::post('/pengajuan/{id}/approve', [KepulanganController::class, 'approvePengajuan'])->name('pengajuan.approve');
        Route::post('/pengajuan/{id}/reject', [KepulanganController::class, 'rejectPengajuan'])->name('pengajuan.reject');
        
        // Settings & Manajemen Kuota
        Route::get('/settings/manage', [KepulanganController::class, 'settings'])->name('settings');
        Route::put('/settings/update', [KepulanganController::class, 'updateSettings'])->name('settings.update');
        
        // List Santri Over Limit
        Route::get('/over-limit/list', [KepulanganController::class, 'santriOverLimit'])->name('over-limit');
        
        // API untuk AJAX (Get Santri Data)
        Route::get('/api/santri/{id_santri}', [KepulanganController::class, 'getSantriData'])->name('api.santri');
        
        // Reset Kuota
        Route::post('/reset/santri/{id_santri}', [KepulanganController::class, 'resetKuotaSantri'])->name('reset.santri');
        Route::post('/reset/semua', [KepulanganController::class, 'resetKuotaSemuaSantri'])->name('reset.semua');
        
        // Main CRUD
        Route::get('/', [KepulanganController::class, 'index'])->name('index');
        Route::get('/create', [KepulanganController::class, 'create'])->name('create');
        Route::post('/', [KepulanganController::class, 'store'])->name('store');
        Route::get('/{id_kepulangan}', [KepulanganController::class, 'show'])->name('show');
        Route::get('/{id_kepulangan}/edit', [KepulanganController::class, 'edit'])->name('edit');
        Route::put('/{id_kepulangan}', [KepulanganController::class, 'update'])->name('update');
        Route::delete('/{id_kepulangan}', [KepulanganController::class, 'destroy'])->name('destroy');
        
        // Actions (Approval/Reject/Complete)
        Route::post('/{id_kepulangan}/approve', [KepulanganController::class, 'approve'])->name('approve');
        Route::post('/{id_kepulangan}/reject', [KepulanganController::class, 'reject'])->name('reject');
        Route::post('/{id_kepulangan}/complete', [KepulanganController::class, 'complete'])->name('complete');
        
        // Print Surat Izin
        Route::get('/{id_kepulangan}/print', [KepulanganController::class, 'print'])->name('print');
        });

    // 6. BERITA
    Route::prefix('berita')->name('berita.')->group(function () {
        Route::get('/', [BeritaController::class, 'index'])->name('index');
        Route::get('/create', [BeritaController::class, 'create'])->name('create');
        Route::post('/', [BeritaController::class, 'store'])->name('store');
        Route::get('/statistik', [BeritaController::class, 'statistik'])->name('statistik');
        Route::get('/{berita:id_berita}', [BeritaController::class, 'show'])->name('show');
        Route::get('/{berita:id_berita}/edit', [BeritaController::class, 'edit'])->name('edit');
        Route::put('/{berita:id_berita}', [BeritaController::class, 'update'])->name('update');
        Route::delete('/{berita:id_berita}', [BeritaController::class, 'destroy'])->name('destroy');
    });

    // 7. KATEGORI PELANGGARAN
    Route::resource('kategori-pelanggaran', KategoriPelanggaranController::class);

    // --- KLASIFIKASI PELANGGARAN (BARU) ---
    Route::resource('klasifikasi-pelanggaran', KlasifikasiPelanggaranController::class);

    // 8. RIWAYAT PELANGGARAN (DENGAN UPDATE KAFAROH & PUBLISH)
    Route::resource('riwayat-pelanggaran', RiwayatPelanggaranController::class);

    Route::prefix('riwayat-pelanggaran')->name('riwayat-pelanggaran.')->group(function () {
        // Route tambahan untuk riwayat per santri
        Route::get('santri/{id_santri}', [
            RiwayatPelanggaranController::class, 
            'riwayatSantri'
        ])->name('riwayat-santri');

        // Route kafaroh & publish
        Route::post('/{riwayatPelanggaran}/selesaikan-kafaroh', [
            RiwayatPelanggaranController::class, 
            'selesaikanKafaroh'
        ])->name('selesaikan-kafaroh');

        Route::post('/{riwayatPelanggaran}/publish-to-parent', [
            RiwayatPelanggaranController::class, 
            'publishToParent'
        ])->name('publish-to-parent');

        Route::post('/{riwayatPelanggaran}/unpublish-from-parent', [
            RiwayatPelanggaranController::class, 
            'unpublishFromParent'
        ])->name('unpublish-from-parent');
    });

    // --- PEMBINAAN & SANKSI (BARU) ---
    Route::resource('pembinaan-sanksi', PembinaanSanksiController::class);

    // 9. PEMBAYARAN SPP
    Route::prefix('pembayaran-spp')->name('pembayaran-spp.')->group(function () {
        Route::get('/', [PembayaranSppController::class, 'index'])->name('index');
        Route::get('/create', [PembayaranSppController::class, 'create'])->name('create');
        Route::post('/', [PembayaranSppController::class, 'store'])->name('store');
        
        // Generate
        Route::get('/generate', [PembayaranSppController::class, 'generate'])->name('generate');
        Route::post('/generate', [PembayaranSppController::class, 'generate']);
        
        // Laporan
        Route::get('/laporan', [PembayaranSppController::class, 'laporan'])->name('laporan');
        Route::get('/cetak-laporan', [PembayaranSppController::class, 'cetakLaporan'])->name('cetak-laporan');
        Route::get('/cetak-laporan-santri/{id_santri}', [PembayaranSppController::class, 'cetakLaporanSantri'])->name('cetak-laporan-santri');
        Route::get('/{pembayaranSpp}/cetak-bukti', [PembayaranSppController::class, 'cetakBukti'])->name('cetak-bukti');
        
        // Riwayat
        Route::get('/riwayat/{id_santri}', [PembayaranSppController::class, 'riwayat'])->name('riwayat');
        
        // Show, Edit, Update, Delete
        Route::get('/{pembayaranSpp}', [PembayaranSppController::class, 'show'])->name('show');
        Route::get('/{pembayaranSpp}/edit', [PembayaranSppController::class, 'edit'])->name('edit');
        Route::put('/{pembayaranSpp}', [PembayaranSppController::class, 'update'])->name('update');
        Route::delete('/{pembayaranSpp}', [PembayaranSppController::class, 'destroy'])->name('destroy');
    });

    // 10. UANG SAKU SANTRI
    Route::prefix('uang-saku')->name('uang-saku.')->group(function () {
        Route::get('/', [UangSakuController::class, 'index'])->name('index');
        Route::get('/create', [UangSakuController::class, 'create'])->name('create');
        Route::post('/', [UangSakuController::class, 'store'])->name('store');
        Route::get('/riwayat/{id_santri}', [UangSakuController::class, 'riwayat'])->name('riwayat');
        Route::get('/{uangSaku}', [UangSakuController::class, 'show'])->name('show');
        Route::get('/{uangSaku}/edit', [UangSakuController::class, 'edit'])->name('edit');
        Route::put('/{uangSaku}', [UangSakuController::class, 'update'])->name('update');
        Route::delete('/{uangSaku}', [UangSakuController::class, 'destroy'])->name('destroy');
    });

    // 11. KATEGORI KEGIATAN
    Route::resource('kategori-kegiatan', KategoriKegiatanController::class);

    // 12. KEGIATAN
    Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
        // Dashboard kegiatan (index)
        Route::get('/', [KegiatanController::class, 'index'])->name('index');
        
        // Jadwal lengkap (harus di atas route {kegiatan})
        Route::get('/jadwal', [KegiatanController::class, 'jadwal'])->name('jadwal');
        
        // CRUD routes (create harus di atas {kegiatan})
        Route::get('/create', [KegiatanController::class, 'create'])->name('create');
        Route::post('/', [KegiatanController::class, 'store'])->name('store');
        
        // Detail routes dengan ID (harus di atas {kegiatan} yang pakai model binding)
        Route::get('/{kegiatan_id}/detail', [KegiatanController::class, 'getDetailModal'])->name('detail-modal');
        Route::get('/{kegiatan}', [KegiatanController::class, 'show'])->name('show');
        Route::get('/{kegiatan}/edit', [KegiatanController::class, 'edit'])->name('edit');
        Route::put('/{kegiatan}', [KegiatanController::class, 'update'])->name('update');
        Route::delete('/{kegiatan}', [KegiatanController::class, 'destroy'])->name('destroy');
    });

    // 13. ABSENSI KEGIATAN
    Route::prefix('absensi-kegiatan')->name('absensi-kegiatan.')->group(function () {
        Route::get('/', [AbsensiKegiatanController::class, 'index'])->name('index');
        Route::get('/input/{kegiatan_id}', [AbsensiKegiatanController::class, 'inputAbsensi'])->name('input');
        Route::post('/simpan', [AbsensiKegiatanController::class, 'simpanAbsensi'])->name('simpan');
        Route::get('/rekap/{kegiatan_id}', [AbsensiKegiatanController::class, 'rekapAbsensi'])->name('rekap');
        Route::post('/scan-rfid', [AbsensiKegiatanController::class, 'scanRfid'])->name('scan-rfid');
    });

    // 14. KARTU RFID
    Route::prefix('kartu-rfid')->name('kartu-rfid.')->group(function () {
        Route::get('/', [KartuRfidController::class, 'index'])->name('index');
        Route::get('/daftar/{id_santri}', [KartuRfidController::class, 'daftarRfid'])->name('daftar');
        Route::post('/simpan/{id_santri}', [KartuRfidController::class, 'simpanRfid'])->name('simpan');
        Route::delete('/hapus/{id_santri}', [KartuRfidController::class, 'hapusRfid'])->name('hapus');
        Route::get('/cetak/{id_santri}', [KartuRfidController::class, 'cetakKartu'])->name('cetak');
    });

    // 15. RIWAYAT KEGIATAN & ABSENSI
    Route::prefix('riwayat-kegiatan')->name('riwayat-kegiatan.')->group(function () {
        Route::get('/', [RiwayatKegiatanController::class, 'index'])->name('index');
        Route::get('/detail-santri/{id_santri}', [RiwayatKegiatanController::class, 'detailSantri'])->name('detail-santri');
        Route::get('/kegiatan/{id}', [RiwayatKegiatanController::class, 'show'])->name('show');
        Route::get('/edit/{riwayat}', [RiwayatKegiatanController::class, 'edit'])->name('edit');
        Route::put('/{riwayat}', [RiwayatKegiatanController::class, 'update'])->name('update');
        Route::delete('/{riwayat}', [RiwayatKegiatanController::class, 'destroy'])->name('destroy');
        Route::get('/export/pdf', [RiwayatKegiatanController::class, 'exportPdf'])->name('export-pdf');
    });

    // 15b. LAPORAN & STATISTIK KEGIATAN (Dashboard Analitik)
    Route::prefix('laporan-kegiatan')->name('laporan-kegiatan.')->group(function () {
        Route::get('/', [LaporanKegiatanController::class, 'index'])->name('index');
        Route::get('/detail-santri/{id_santri}', [LaporanKegiatanController::class, 'detailSantri'])->name('detail-santri');
        Route::get('/santri-perlu-perhatian', [LaporanKegiatanController::class, 'santriPerluPerhatian'])->name('santri-perlu-perhatian');
        Route::get('/leaderboard', [LaporanKegiatanController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/analisis-kegiatan/{kegiatan_id}', [LaporanKegiatanController::class, 'analisKegiatan'])->name('analisis-kegiatan');
        Route::get('/analisis-kelas', [LaporanKegiatanController::class, 'analisPerKelas'])->name('analisis-kelas');
        Route::get('/patterns', [LaporanKegiatanController::class, 'patternDetection'])->name('patterns');
        Route::get('/export-excel', [LaporanKegiatanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [LaporanKegiatanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/refresh-kpi', [LaporanKegiatanController::class, 'refreshKpi'])->name('refresh-kpi');
    });

    // 16. MASTER MATERI (Capaian Al-Qur'an & Hadist)
    Route::resource('materi', MateriController::class);

    // 17. SEMESTER (Capaian Al-Qur'an & Hadist)
    Route::resource('semester', SemesterController::class);
    Route::post('semester/{semester}/toggle-aktif', [SemesterController::class, 'toggleAktif'])->name('semester.toggle-aktif');

    // 18. KELOLA KELAS (Sistem Kelas Baru)
    // IMPORTANT: Kelompok dan Kenaikan routes HARUS sebelum resource route untuk menghindari konflik
    
    // 21b. Kelompok Kelas Management
    Route::prefix('kelas/kelompok')->name('kelas.kelompok.')->group(function () {
        Route::get('/', [KelasController::class, 'kelompokIndex'])->name('index');
        Route::get('/create', [KelasController::class, 'kelompokCreate'])->name('create');
        Route::post('/', [KelasController::class, 'kelompokStore'])->name('store');
        Route::get('/{id}/edit', [KelasController::class, 'kelompokEdit'])->name('edit');
        Route::put('/{id}', [KelasController::class, 'kelompokUpdate'])->name('update');
        Route::delete('/{id}', [KelasController::class, 'kelompokDestroy'])->name('destroy');
    });
    
    // 21c. Kenaikan Kelas Massal
    Route::prefix('kelas/kenaikan')->name('kelas.kenaikan.')->group(function () {
        Route::get('/', [KelasController::class, 'kenaikanIndex'])->name('index');
        Route::get('/preview/{id}', [KelasController::class, 'kenaikanPreview'])->name('preview');
        Route::post('/process', [KelasController::class, 'kenaikanProcess'])->name('process');
        Route::post('/process-selected', [KelasController::class, 'kenaikanProcessSelected'])->name('process-selected');
    });
    
    // 21a. CRUD Kelas (Main) - HARUS SETELAH route prefix di atas
    Route::resource('kelas', KelasController::class);


    // 19. CAPAIAN SANTRI (Al-Qur'an & Hadist)
    Route::prefix('capaian')->name('capaian.')->group(function () {
        // Dashboard & Rekap
        Route::get('/dashboard', [CapaianController::class, 'dashboard'])->name('dashboard');
        Route::get('/detail-materi/{id_materi}', [CapaianController::class, 'detailMateri'])->name('detail-materi');
        
        // Tandai Khatam & Export
        Route::post('/tandai-khatam/{id_santri}', [CapaianController::class, 'tandaiKhatam'])->name('tandai-khatam');
        Route::post('/batal-khatam/{id_santri}', [CapaianController::class, 'batalKhatam'])->name('batal-khatam');
        Route::get('/export-rapor/{id_santri}/{id_semester}', [CapaianController::class, 'exportRapor'])->name('export-rapor');
        
        // CRUD Capaian
        Route::get('/', [CapaianController::class, 'index'])->name('index');
        Route::get('/create', [CapaianController::class, 'create'])->name('create');
        Route::post('/', [CapaianController::class, 'store'])->name('store');
        Route::get('/riwayat/{id_santri}', [CapaianController::class, 'riwayatSantri'])->name('riwayat-santri');
        Route::get('/{capaian}', [CapaianController::class, 'show'])->name('show');
        Route::get('/{capaian}/edit', [CapaianController::class, 'edit'])->name('edit');
        Route::put('/{capaian}', [CapaianController::class, 'update'])->name('update');
        Route::delete('/{capaian}', [CapaianController::class, 'destroy'])->name('destroy');
        
        // AJAX Routes
        Route::post('/ajax/get-materi', [CapaianController::class, 'getMateriByKelas'])->name('ajax.get-materi');
        Route::post('/ajax/get-detail-materi', [CapaianController::class, 'getDetailMateri'])->name('ajax.get-detail-materi');
        Route::post('/ajax/calculate-persentase', [CapaianController::class, 'calculatePersentase'])->name('ajax.calculate-persentase');
        
        // API untuk Grafik
        Route::get('/api/grafik-data', [CapaianController::class, 'apiGrafikData'])->name('api.grafik-data');
    });
});

/*
|--------------------------------------------------------------------------
| BAGIAN SANTRI/WALI ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('santri')
    ->middleware(['auth', 'role:santri,wali'])
    ->name('santri.')
    ->group(function () {
    
    // Logout Santri
    Route::post('logout', [SantriAuthController::class, 'logout'])->name('logout');
    
    // Dashboard Santri
    Route::get('/dashboard', [DashboardController::class, 'santri'])->name('dashboard');
    
    // 1. PROFIL SANTRI
    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/', [SantriProfileController::class, 'index'])->name('index');
        Route::get('/edit', [SantriProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [SantriProfileController::class, 'update'])->name('update');
    });
    
    // 2. RIWAYAT UANG SAKU
    Route::prefix('uang-saku')->name('uang-saku.')->group(function () {
        Route::get('/', [SantriUangSakuController::class, 'index'])->name('index');
        Route::get('/{id}', [SantriUangSakuController::class, 'show'])->name('show');
    });
    
    // 3. RIWAYAT PELANGGARAN
    Route::prefix('pelanggaran')->name('pelanggaran.')->group(function () {
        Route::get('/', [SantriPelanggaranController::class, 'index'])->name('index');
        Route::get('/kategori/daftar', [SantriPelanggaranController::class, 'kategoriList'])->name('kategori');
        Route::get('/{riwayatPelanggaran}', [SantriPelanggaranController::class, 'show'])->name('show');
    });

    // 4. BERITA SANTRI
    Route::prefix('berita')->name('berita.')->group(function () {
        Route::get('/', [SantriBeritaController::class, 'index'])->name('index');
        Route::get('/{berita:id_berita}', [SantriBeritaController::class, 'show'])->name('show');
    });
    
    // 5. RIWAYAT KESEHATAN
    Route::prefix('kesehatan')->name('kesehatan.')->group(function () {
        Route::get('/', [SantriKesehatanController::class, 'index'])->name('index');
        Route::get('/{kesehatanSantri}', [SantriKesehatanController::class, 'show'])->name('show');
    });
    
    // 6. CAPAIAN AL-QUR'AN & HADIST
    Route::prefix('capaian')->name('capaian.')->group(function () {
        Route::get('/', [SantriCapaianController::class, 'index'])->name('index');
        Route::get('/{id}', [SantriCapaianController::class, 'show'])->name('show');
        Route::get('/api/grafik-data', [SantriCapaianController::class, 'apiGrafikData'])->name('api.grafik-data');
    });

    // 7. RIWAYAT KEPULANGAN
    Route::prefix('kepulangan')->name('kepulangan.')->group(function () {
        Route::get('/', [SantriKepulanganController::class, 'index'])->name('index');
        Route::get('/{kepulangan:id_kepulangan}', [SantriKepulanganController::class, 'show'])->name('show');
    });

    // 8. RIWAYAT KEGIATAN & ABSENSI (BARU ✅)
    Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
        Route::get('/', [RiwayatKegiatanSantriController::class, 'index'])->name('index');
        Route::get('/{kegiatan_id}', [RiwayatKegiatanSantriController::class, 'show'])->name('show');
    });
});