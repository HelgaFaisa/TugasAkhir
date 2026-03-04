<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SantriController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KesehatanSantriController;
use App\Http\Controllers\Admin\KepulanganController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\KategoriPelanggaranController;
use App\Http\Controllers\Admin\RiwayatPelanggaranController;
use App\Http\Controllers\Admin\KlasifikasiPelanggaranController;
use App\Http\Controllers\Admin\PembinaanSanksiController;
use App\Http\Controllers\Admin\PembayaranSppController;
use App\Http\Controllers\Admin\UangSakuController;
use App\Http\Controllers\Admin\KategoriKegiatanController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\AbsensiKegiatanController;
use App\Http\Controllers\Admin\KartuRfidController;
use App\Http\Controllers\Admin\RiwayatKegiatanController;
use App\Http\Controllers\Admin\LaporanKegiatanController;
use App\Http\Controllers\Admin\MateriController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\CapaianController;
use App\Http\Controllers\Admin\ImportMesinController;
use App\Http\Controllers\Admin\MesinMappingController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Auth\SantriAuthController;
use App\Http\Controllers\Santri\SantriProfileController;
use App\Http\Controllers\Santri\SantriUangSakuController;
use App\Http\Controllers\Santri\SantriPelanggaranController;
use App\Http\Controllers\Santri\SantriBeritaController;
use App\Http\Controllers\Santri\SantriKesehatanController;
use App\Http\Controllers\Santri\SantriCapaianController;
use App\Http\Controllers\Santri\SantriCapaianInputController;
use App\Http\Controllers\Santri\SantriKepulanganController;
use App\Http\Controllers\Santri\RiwayatKegiatanSantriController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// -- Fallback route login untuk middleware authenticate --
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// -- Redirect root ke login admin --
Route::get('/', function () {
    return redirect()->route('admin.login');
})->name('home');

// -- Auth Admin (guest only) --
Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::get('login',     [AdminAuthController::class, 'login'])->name('login');
    Route::post('login',    [AdminAuthController::class, 'authenticate'])->name('authenticate');
    Route::get('register',  [AdminAuthController::class, 'register'])->name('register');
    Route::post('register', [AdminAuthController::class, 'storeRegister'])->name('store-register');

    // -- Lupa Password (Super Admin) --
    Route::get('forgot-password',         [AdminForgotPasswordController::class, 'showEmailForm'])->name('forgot.email_form');
    Route::post('forgot-password',        [AdminForgotPasswordController::class, 'sendOtp'])->name('forgot.send_otp');
    Route::get('forgot-password/verify',  [AdminForgotPasswordController::class, 'showVerifyForm'])->name('forgot.verify_form');
    Route::post('forgot-password/verify', [AdminForgotPasswordController::class, 'verifyOtp'])->name('forgot.verify_otp');
    Route::post('forgot-password/resend', [AdminForgotPasswordController::class, 'resendOtp'])->name('forgot.resend_otp');
    Route::get('forgot-password/reset',   [AdminForgotPasswordController::class, 'showResetForm'])->name('forgot.reset_form');
    Route::post('forgot-password/reset',  [AdminForgotPasswordController::class, 'resetPassword'])->name('forgot.reset_password');
});

// -- Auth Santri (guest only) --
Route::prefix('santri')->name('santri.')->middleware('guest')->group(function () {
    Route::get('login',  [SantriAuthController::class, 'login'])->name('login');
    Route::post('login', [SantriAuthController::class, 'authenticate'])->name('authenticate');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // ================================================================
    // SEMUA ROLE ADMIN (super_admin, akademik, pamong)
    // ================================================================
    Route::middleware(['auth', 'role:super_admin,akademik,pamong'])->group(function () {

        // -- Logout --
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        // -- Dashboard --
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    });

    // ================================================================
    // SUPER ADMIN ONLY
    // ================================================================
    Route::middleware(['auth', 'role:super_admin'])->group(function () {

        // -- Manajemen User --
        Route::prefix('users')->name('users.')->group(function () {

            // Akun Santri (Web Login)
            Route::get('santri', [UserController::class, 'santriAccounts'])
                ->name('santri_accounts');
            Route::post('santri/{idSantri}/buat-akun', [UserController::class, 'buatAkunSantri'])
                ->name('santri_buat_akun');
            Route::post('santri/buat-semua', [UserController::class, 'buatSemuaAkunSantri'])
                ->name('santri_buat_semua');
            Route::post('santri/{id}/hapus', [UserController::class, 'destroySantriAccount'])
                ->name('santri_destroy');

            // Akun Wali (Mobile Login)
            Route::get('wali', [UserController::class, 'waliAccounts'])
                ->name('wali_accounts');
            Route::post('wali/{idSantri}/buat-akun', [UserController::class, 'buatAkunWali'])
                ->name('wali_buat_akun');
            Route::post('wali/buat-semua', [UserController::class, 'buatSemuaAkunWali'])
                ->name('wali_buat_semua');
            Route::post('wali/{id}/hapus', [UserController::class, 'destroyWaliAccount'])
                ->name('wali_destroy');

            // Akun Admin (akademik & pamong)
            Route::get('admin',               [UserController::class, 'adminAccounts'])->name('admin_accounts');
            Route::get('admin/create',        [UserController::class, 'createAdminAccount'])->name('admin_create');
            Route::post('admin/store',        [UserController::class, 'storeAdminAccount'])->name('admin_store');
            Route::get('admin/{userId}/edit', [UserController::class, 'editAdminAccount'])->name('admin_edit');
            Route::put('admin/{userId}',      [UserController::class, 'updateAdminAccount'])->name('admin_update');
            Route::delete('admin/{userId}/hapus', [UserController::class, 'destroyAdminAccount'])->name('admin_destroy');
        });

        // -- Keuangan Pondok --
        Route::prefix('keuangan')->name('keuangan.')->group(function () {
            Route::get('/laporan',       [KeuanganController::class, 'laporan'])->name('laporan');
            Route::get('/',              [KeuanganController::class, 'index'])->name('index');
            Route::get('/create',        [KeuanganController::class, 'create'])->name('create');
            Route::post('/',             [KeuanganController::class, 'store'])->name('store');
            Route::get('/{keuangan}',    [KeuanganController::class, 'show'])->name('show');
            Route::get('/{keuangan}/edit', [KeuanganController::class, 'edit'])->name('edit');
            Route::put('/{keuangan}',    [KeuanganController::class, 'update'])->name('update');
            Route::delete('/{keuangan}', [KeuanganController::class, 'destroy'])->name('destroy');
        });

        // -- Pembayaran SPP --
        Route::prefix('pembayaran-spp')->name('pembayaran-spp.')->group(function () {
            Route::get('/',              [PembayaranSppController::class, 'index'])->name('index');
            Route::get('/create',        [PembayaranSppController::class, 'create'])->name('create');
            Route::post('/',             [PembayaranSppController::class, 'store'])->name('store');
            Route::get('/generate',      [PembayaranSppController::class, 'generate'])->name('generate');
            Route::post('/generate',     [PembayaranSppController::class, 'generate']);
            Route::get('/laporan',       [PembayaranSppController::class, 'laporan'])->name('laporan');
            Route::get('/cetak-laporan', [PembayaranSppController::class, 'cetakLaporan'])->name('cetak-laporan');
            Route::get('/cetak-laporan-santri/{id_santri}',
                [PembayaranSppController::class, 'cetakLaporanSantri'])->name('cetak-laporan-santri');
            Route::get('/{pembayaranSpp}/cetak-bukti',
                [PembayaranSppController::class, 'cetakBukti'])->name('cetak-bukti');
            Route::get('/riwayat/{id_santri}', [PembayaranSppController::class, 'riwayat'])->name('riwayat');
            Route::get('/{pembayaranSpp}',     [PembayaranSppController::class, 'show'])->name('show');
            Route::get('/{pembayaranSpp}/edit',[PembayaranSppController::class, 'edit'])->name('edit');
            Route::put('/{pembayaranSpp}',     [PembayaranSppController::class, 'update'])->name('update');
            Route::delete('/{pembayaranSpp}',  [PembayaranSppController::class, 'destroy'])->name('destroy');
            Route::post('/{pembayaranSpp}/bayar',   [PembayaranSppController::class, 'bayar'])->name('bayar');
            Route::post('/{pembayaranSpp}/cicilan', [PembayaranSppController::class, 'catatCicilan'])->name('cicilan');
        });

        // ================================================================
        // IMPORT MESIN EPPOS — SUPER ADMIN ONLY
        // ================================================================
        Route::prefix('mesin')->name('mesin.')->group(function () {

            // -- Import GLog.txt --
            // Alur: upload form (GET index)
            //       → submit (POST preview) → proses → simpan session → redirect
            //       → tampil hasil (GET show-preview) ← aman di-refresh
            //       → simpan ke DB (POST store)

            Route::get('/import',
                [ImportMesinController::class, 'index'])
                ->name('import.index');

            Route::post('/import/preview',
                [ImportMesinController::class, 'preview'])
                ->name('import.preview');

            Route::get('/import/show-preview',          // ← ROUTE BARU: GET, aman di-refresh
                [ImportMesinController::class, 'showPreview'])
                ->name('import.show-preview');

            Route::post('/import/store',
                [ImportMesinController::class, 'store'])
                ->name('import.store');

            // -- Mapping ID Mesin ↔ Santri --
            Route::get('/mapping-santri',
                [MesinMappingController::class, 'index'])
                ->name('mapping-santri.index');

            Route::post('/mapping-santri',
                [MesinMappingController::class, 'store'])
                ->name('mapping-santri.store');

            Route::put('/mapping-santri/{id}',
                [MesinMappingController::class, 'update'])
                ->name('mapping-santri.update');

            Route::delete('/mapping-santri/{id}',
                [MesinMappingController::class, 'destroy'])
                ->name('mapping-santri.destroy');

            Route::post('/mapping-santri/import-info',
                [MesinMappingController::class, 'importFromInfo'])
                ->name('mapping-santri.import-info');
        });
        // ================================================================
    });

    // ================================================================
    // SUPER ADMIN + AKADEMIK
    // ================================================================
    Route::middleware(['auth', 'role:super_admin,akademik'])->group(function () {

        // -- Santri CUD (create, update, delete) --
        Route::resource('santri', SantriController::class)->except(['index', 'show']);

        // -- Kelompok Kelas --
        Route::prefix('kelas/kelompok')->name('kelas.kelompok.')->group(function () {
            Route::get('/',          [KelasController::class, 'kelompokIndex'])->name('index');
            Route::get('/create',    [KelasController::class, 'kelompokCreate'])->name('create');
            Route::post('/',         [KelasController::class, 'kelompokStore'])->name('store');
            Route::get('/{id}/edit', [KelasController::class, 'kelompokEdit'])->name('edit');
            Route::put('/{id}',      [KelasController::class, 'kelompokUpdate'])->name('update');
            Route::delete('/{id}',   [KelasController::class, 'kelompokDestroy'])->name('destroy');
        });

        // -- Kenaikan Kelas --
        Route::prefix('kelas/kenaikan')->name('kelas.kenaikan.')->group(function () {
            Route::get('/',             [KelasController::class, 'kenaikanIndex'])->name('index');
            Route::get('/preview/{id}', [KelasController::class, 'kenaikanPreview'])->name('preview');
            Route::post('/process',     [KelasController::class, 'kenaikanProcess'])->name('process');
            Route::post('/process-selected', [KelasController::class, 'kenaikanProcessSelected'])
                ->name('process-selected');
        });

        // -- Kelas CRUD --
        Route::resource('kelas', KelasController::class);

        // -- Kegiatan CUD --
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            Route::get('/create',          [KegiatanController::class, 'create'])->name('create');
            Route::post('/',               [KegiatanController::class, 'store'])->name('store');
            Route::get('/{kegiatan}/edit', [KegiatanController::class, 'edit'])->name('edit');
            Route::put('/{kegiatan}',      [KegiatanController::class, 'update'])->name('update');
            Route::delete('/{kegiatan}',   [KegiatanController::class, 'destroy'])->name('destroy');
        });

        // -- Kartu RFID --
        Route::prefix('kartu-rfid')->name('kartu-rfid.')->group(function () {
            Route::get('/',                     [KartuRfidController::class, 'index'])->name('index');
            Route::get('/daftar/{id_santri}',   [KartuRfidController::class, 'daftarRfid'])->name('daftar');
            Route::post('/simpan/{id_santri}',  [KartuRfidController::class, 'simpanRfid'])->name('simpan');
            Route::delete('/hapus/{id_santri}', [KartuRfidController::class, 'hapusRfid'])->name('hapus');
            Route::get('/cetak/{id_santri}',    [KartuRfidController::class, 'cetakKartu'])->name('cetak');
        });

        // -- Rekap Absensi --
        Route::get('absensi-kegiatan/rekap/{kegiatan_id}',
            [AbsensiKegiatanController::class, 'rekapAbsensi'])
            ->name('absensi-kegiatan.rekap');

        // -- Riwayat Kegiatan --
        Route::prefix('riwayat-kegiatan')->name('riwayat-kegiatan.')->group(function () {
            Route::get('/',                          [RiwayatKegiatanController::class, 'index'])->name('index');
            Route::get('/detail-santri/{id_santri}', [RiwayatKegiatanController::class, 'detailSantri'])->name('detail-santri');
            Route::get('/kegiatan/{id}',             [RiwayatKegiatanController::class, 'show'])->name('show');
            Route::get('/edit/{riwayat}',            [RiwayatKegiatanController::class, 'edit'])->name('edit');
            Route::put('/{riwayat}',                 [RiwayatKegiatanController::class, 'update'])->name('update');
            Route::delete('/{riwayat}',              [RiwayatKegiatanController::class, 'destroy'])->name('destroy');
            Route::get('/export/pdf',                [RiwayatKegiatanController::class, 'exportPdf'])->name('export-pdf');
        });

        // -- Laporan Kegiatan --
        Route::prefix('laporan-kegiatan')->name('laporan-kegiatan.')->group(function () {
            Route::get('/',                                [LaporanKegiatanController::class, 'index'])->name('index');
            Route::get('/detail-santri/{id_santri}',       [LaporanKegiatanController::class, 'detailSantri'])->name('detail-santri');
            Route::get('/santri-perlu-perhatian',          [LaporanKegiatanController::class, 'santriPerluPerhatian'])->name('santri-perlu-perhatian');
            Route::get('/leaderboard',                     [LaporanKegiatanController::class, 'leaderboard'])->name('leaderboard');
            Route::get('/analisis-kegiatan/{kegiatan_id}', [LaporanKegiatanController::class, 'analisKegiatan'])->name('analisis-kegiatan');
            Route::get('/analisis-kelas',                  [LaporanKegiatanController::class, 'analisPerKelas'])->name('analisis-kelas');
            Route::get('/patterns',                        [LaporanKegiatanController::class, 'patternDetection'])->name('patterns');
            Route::get('/export-excel',                    [LaporanKegiatanController::class, 'exportExcel'])->name('export-excel');
            Route::get('/export-pdf',                      [LaporanKegiatanController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/refresh-kpi',                     [LaporanKegiatanController::class, 'refreshKpi'])->name('refresh-kpi');
        });

        // -- Materi & Semester --
        Route::resource('materi', MateriController::class);
        Route::resource('semester', SemesterController::class);
        Route::post('semester/{semester}/toggle-aktif', [SemesterController::class, 'toggleAktif'])
            ->name('semester.toggle-aktif');

        // -- Pelanggaran --
        Route::resource('kategori-pelanggaran', KategoriPelanggaranController::class);
        Route::resource('klasifikasi-pelanggaran', KlasifikasiPelanggaranController::class);
        Route::resource('riwayat-pelanggaran', RiwayatPelanggaranController::class);

        Route::prefix('riwayat-pelanggaran')->name('riwayat-pelanggaran.')->group(function () {
            Route::get('santri/{id_santri}',
                [RiwayatPelanggaranController::class, 'riwayatSantri'])->name('riwayat-santri');
            Route::post('/{riwayatPelanggaran}/selesaikan-kafaroh',
                [RiwayatPelanggaranController::class, 'selesaikanKafaroh'])->name('selesaikan-kafaroh');
            Route::post('/{riwayatPelanggaran}/publish-to-parent',
                [RiwayatPelanggaranController::class, 'publishToParent'])->name('publish-to-parent');
            Route::post('/{riwayatPelanggaran}/unpublish-from-parent',
                [RiwayatPelanggaranController::class, 'unpublishFromParent'])->name('unpublish-from-parent');
        });

        Route::resource('pembinaan-sanksi', PembinaanSanksiController::class);

        // -- Berita --
        Route::prefix('berita')->name('berita.')->group(function () {
            Route::get('/',                       [BeritaController::class, 'index'])->name('index');
            Route::get('/create',                 [BeritaController::class, 'create'])->name('create');
            Route::post('/',                      [BeritaController::class, 'store'])->name('store');
            Route::get('/statistik',              [BeritaController::class, 'statistik'])->name('statistik');
            Route::get('/{berita:id_berita}',     [BeritaController::class, 'show'])->name('show');
            Route::get('/{berita:id_berita}/edit',[BeritaController::class, 'edit'])->name('edit');
            Route::put('/{berita:id_berita}',     [BeritaController::class, 'update'])->name('update');
            Route::delete('/{berita:id_berita}',  [BeritaController::class, 'destroy'])->name('destroy');
        });

        // -- Kategori Kegiatan --
        Route::resource('kategori-kegiatan', KategoriKegiatanController::class);
    });

    // ================================================================
    // SUPER ADMIN + AKADEMIK + PAMONG
    // ================================================================
    Route::middleware(['auth', 'role:super_admin,akademik,pamong'])->group(function () {

        // -- Santri GET only --
        Route::get('santri',         [SantriController::class, 'index'])->name('santri.index');
        Route::get('santri/{santri}',[SantriController::class, 'show'])->name('santri.show');

        // -- Kegiatan GET only --
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            Route::get('/',                     [KegiatanController::class, 'index'])->name('index');
            Route::get('/jadwal',               [KegiatanController::class, 'jadwal'])->name('jadwal');
            Route::get('/{kegiatan_id}/detail', [KegiatanController::class, 'getDetailModal'])->name('detail-modal');
            Route::get('/{kegiatan}',           [KegiatanController::class, 'show'])->name('show');
        });

        // -- Absensi Kegiatan + RFID USB --
        Route::prefix('absensi-kegiatan')->name('absensi-kegiatan.')->group(function () {
            Route::get('/',                    [AbsensiKegiatanController::class, 'index'])->name('index');
            Route::get('/input/{kegiatan_id}', [AbsensiKegiatanController::class, 'inputAbsensi'])->name('input');
            Route::post('/simpan',             [AbsensiKegiatanController::class, 'simpanAbsensi'])->name('simpan');
            Route::post('/scan-rfid',          [AbsensiKegiatanController::class, 'scanRfid'])->name('scan-rfid');
            Route::get('/edit/{id}',           [AbsensiKegiatanController::class, 'editAbsensi'])->name('edit');
            Route::put('/update/{id}',         [AbsensiKegiatanController::class, 'updateAbsensi'])->name('update');
            Route::delete('/hapus/{id}',       [AbsensiKegiatanController::class, 'hapusAbsensi'])->name('hapus');
        });

        // -- Capaian Santri --
        Route::prefix('capaian')->name('capaian.')->group(function () {
            Route::get('/dashboard',                 [CapaianController::class, 'dashboard'])->name('dashboard');
            Route::get('/detail-materi/{id_materi}', [CapaianController::class, 'detailMateri'])->name('detail-materi');
            Route::post('/tandai-khatam/{id_santri}',[CapaianController::class, 'tandaiKhatam'])->name('tandai-khatam');
            Route::post('/batal-khatam/{id_santri}', [CapaianController::class, 'batalKhatam'])->name('batal-khatam');
            Route::get('/export-rapor/{id_santri}/{id_semester}',
                [CapaianController::class, 'exportRapor'])->name('export-rapor');

            Route::get('/akses-santri',        [CapaianController::class, 'kelolaAksesSantri'])->name('akses-santri');
            Route::post('/akses-santri/buka',  [CapaianController::class, 'bukaAksesSantri'])->name('akses-santri.buka');
            Route::post('/akses-santri/tutup', [CapaianController::class, 'tutupAksesSantri'])->name('akses-santri.tutup');

            Route::get('/',                    [CapaianController::class, 'index'])->name('index');
            Route::get('/create',              [CapaianController::class, 'create'])->name('create');
            Route::post('/',                   [CapaianController::class, 'store'])->name('store');
            Route::get('/riwayat/{id_santri}', [CapaianController::class, 'riwayatSantri'])->name('riwayat-santri');
            Route::get('/{capaian}',           [CapaianController::class, 'show'])->name('show');
            Route::get('/{capaian}/edit',      [CapaianController::class, 'edit'])->name('edit');
            Route::put('/{capaian}',           [CapaianController::class, 'update'])->name('update');
            Route::delete('/{capaian}',        [CapaianController::class, 'destroy'])->name('destroy');
            Route::post('/ajax/get-materi',           [CapaianController::class, 'getMateriByKelas'])->name('ajax.get-materi');
            Route::post('/ajax/get-detail-materi',    [CapaianController::class, 'getDetailMateri'])->name('ajax.get-detail-materi');
            Route::post('/ajax/calculate-persentase', [CapaianController::class, 'calculatePersentase'])->name('ajax.calculate-persentase');
            Route::get('/api/grafik-data',            [CapaianController::class, 'apiGrafikData'])->name('api.grafik-data');
        });

        // -- Kesehatan Santri --
        Route::resource('kesehatan-santri', KesehatanSantriController::class);
        Route::patch('kesehatan-santri/{kesehatanSantri}/keluar-ukp',
            [KesehatanSantriController::class, 'keluarUkp'])->name('kesehatan-santri.keluar-ukp');
        Route::get('kesehatan-santri/riwayat/{id_santri}',
            [KesehatanSantriController::class, 'riwayat'])->name('kesehatan-santri.riwayat');
        Route::get('kesehatan-santri/{kesehatanSantri}/cetak-surat',
            [KesehatanSantriController::class, 'cetakSurat'])->name('kesehatan-santri.cetak-surat');

        // -- Kepulangan --
        Route::prefix('kepulangan')->name('kepulangan.')->group(function () {
            Route::get('/pengajuan',              [KepulanganController::class, 'pengajuan'])->name('pengajuan');
            Route::post('/pengajuan/{id}/approve',[KepulanganController::class, 'approvePengajuan'])->name('pengajuan.approve');
            Route::post('/pengajuan/{id}/reject', [KepulanganController::class, 'rejectPengajuan'])->name('pengajuan.reject');
            Route::get('/settings/manage',        [KepulanganController::class, 'settings'])->name('settings');
            Route::put('/settings/update',        [KepulanganController::class, 'updateSettings'])->name('settings.update');
            Route::get('/over-limit/list',        [KepulanganController::class, 'santriOverLimit'])->name('over-limit');
            Route::get('/api/santri/{id_santri}', [KepulanganController::class, 'getSantriData'])->name('api.santri');
            Route::post('/reset/santri/{id_santri}',
                [KepulanganController::class, 'resetKuotaSantri'])->name('reset.santri');
            Route::post('/reset/semua',
                [KepulanganController::class, 'resetKuotaSemuaSantri'])->name('reset.semua');
            Route::get('/',                       [KepulanganController::class, 'index'])->name('index');
            Route::get('/create',                 [KepulanganController::class, 'create'])->name('create');
            Route::post('/',                      [KepulanganController::class, 'store'])->name('store');
            Route::get('/{id_kepulangan}',        [KepulanganController::class, 'show'])->name('show');
            Route::get('/{id_kepulangan}/edit',   [KepulanganController::class, 'edit'])->name('edit');
            Route::put('/{id_kepulangan}',        [KepulanganController::class, 'update'])->name('update');
            Route::delete('/{id_kepulangan}',     [KepulanganController::class, 'destroy'])->name('destroy');
            Route::post('/{id_kepulangan}/approve',  [KepulanganController::class, 'approve'])->name('approve');
            Route::post('/{id_kepulangan}/reject',   [KepulanganController::class, 'reject'])->name('reject');
            Route::post('/{id_kepulangan}/complete', [KepulanganController::class, 'complete'])->name('complete');
            Route::get('/{id_kepulangan}/print',     [KepulanganController::class, 'print'])->name('print');
        });
    });

    // ================================================================
    // SUPER ADMIN + PAMONG
    // ================================================================
    Route::middleware(['auth', 'role:super_admin,pamong'])->group(function () {

        // -- Uang Saku --
        Route::prefix('uang-saku')->name('uang-saku.')->group(function () {
            Route::get('/',                       [UangSakuController::class, 'index'])->name('index');
            Route::get('/create',                 [UangSakuController::class, 'create'])->name('create');
            Route::post('/',                      [UangSakuController::class, 'store'])->name('store');
            Route::get('/santri-info/{id_santri}',[UangSakuController::class, 'santriInfo'])->name('santri-info');
            Route::get('/riwayat/{id_santri}',    [UangSakuController::class, 'riwayat'])->name('riwayat');
            Route::get('/{uangSaku}',             [UangSakuController::class, 'show'])->name('show');
            Route::get('/{uangSaku}/edit',        [UangSakuController::class, 'edit'])->name('edit');
            Route::put('/{uangSaku}',             [UangSakuController::class, 'update'])->name('update');
            Route::delete('/{uangSaku}',          [UangSakuController::class, 'destroy'])->name('destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| SANTRI / WALI ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('santri')
    ->middleware(['santri.auth'])
    ->name('santri.')
    ->group(function () {

    // -- Logout --
    Route::post('logout', [SantriAuthController::class, 'logout'])->name('logout');

    // -- Dashboard --
    Route::get('/dashboard', [DashboardController::class, 'santri'])->name('dashboard');

    // -- Profil --
    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/',       [SantriProfileController::class, 'index'])->name('index');
        Route::get('/edit',   [SantriProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [SantriProfileController::class, 'update'])->name('update');
    });

    // -- Uang Saku --
    Route::prefix('uang-saku')->name('uang-saku.')->group(function () {
        Route::get('/',     [SantriUangSakuController::class, 'index'])->name('index');
        Route::get('/{id}', [SantriUangSakuController::class, 'show'])->name('show');
    });

    // -- Pelanggaran --
    Route::prefix('pelanggaran')->name('pelanggaran.')->group(function () {
        Route::get('/',                     [SantriPelanggaranController::class, 'index'])->name('index');
        Route::get('/kategori/daftar',      [SantriPelanggaranController::class, 'kategoriList'])->name('kategori');
        Route::get('/{riwayatPelanggaran}', [SantriPelanggaranController::class, 'show'])->name('show');
    });

    // -- Berita --
    Route::prefix('berita')->name('berita.')->group(function () {
        Route::get('/',                  [SantriBeritaController::class, 'index'])->name('index');
        Route::get('/{berita:id_berita}',[SantriBeritaController::class, 'show'])->name('show');
    });

    // -- Kesehatan --
    Route::prefix('kesehatan')->name('kesehatan.')->group(function () {
        Route::get('/',                  [SantriKesehatanController::class, 'index'])->name('index');
        Route::get('/{kesehatanSantri}', [SantriKesehatanController::class, 'show'])->name('show');
    });

    // -- Capaian --
    Route::prefix('capaian')->name('capaian.')->group(function () {
        Route::get('/', [SantriCapaianController::class, 'index'])->name('index');

        Route::get('/input',              [SantriCapaianInputController::class, 'create'])->name('input.create');
        Route::post('/input',             [SantriCapaianInputController::class, 'store'])->name('input.store');
        Route::post('/input/ajax/detail', [SantriCapaianInputController::class, 'ajaxDetailMateri'])->name('input.ajax.detail');
        Route::post('/input/ajax/hitung', [SantriCapaianInputController::class, 'ajaxHitungPersentase'])->name('input.ajax.hitung');

        Route::get('/api/grafik-data', [SantriCapaianController::class, 'apiGrafikData'])->name('api.grafik-data');
        Route::get('/{id}',            [SantriCapaianController::class, 'show'])->name('show');
    });

    // -- Kepulangan --
    Route::prefix('kepulangan')->name('kepulangan.')->group(function () {
        Route::get('/',                           [SantriKepulanganController::class, 'index'])->name('index');
        Route::get('/{kepulangan:id_kepulangan}', [SantriKepulanganController::class, 'show'])->name('show');
    });

    // -- Pembinaan & Sanksi --
    Route::prefix('pembinaan')->name('pembinaan.')->group(function () {
        Route::get('/',              [\App\Http\Controllers\Santri\SantriPembinaanController::class, 'index'])->name('index');
        Route::get('/{id_pembinaan}',[\App\Http\Controllers\Santri\SantriPembinaanController::class, 'show'])->name('show');
    });

    // -- Kegiatan & Absensi --
    Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
        Route::get('/',             [RiwayatKegiatanSantriController::class, 'index'])->name('index');
        Route::get('/{kegiatan_id}',[RiwayatKegiatanSantriController::class, 'show'])->name('show');
    });
});