# Dokumentasi RBAC (Role-Based Access Control) — SIM-PKPPS

## 1. Ringkasan

Sistem RBAC telah diimplementasikan untuk memisahkan hak akses 3 jenis admin:

| Role | Deskripsi |
|------|-----------|
| **super_admin** | Akses penuh ke semua fitur, termasuk keuangan & SPP |
| **akademik** | Fokus data akademik: santri, kelas, kegiatan, materi, pelanggaran, berita |
| **pamong** | Fokus pengasuhan: uang saku, absensi, kesehatan, kepulangan |

Role lain (`santri`, `wali`) tidak terpengaruh — tetap berjalan seperti sebelumnya.

---

## 2. Akun Test

| Role | Username / Email | Password |
|------|-----------------|----------|
| super_admin | `helga.faisa06@gmail.com` | `12345678` |
| akademik | `akademik@test.com` | `password123` |
| pamong | `pamong@test.com` | `password123` |

Login di: **http://127.0.0.1:8000/admin/login**

> **Penting:** Jika mengalami redirect loop di browser, bersihkan cookies terlebih dahulu (Ctrl+Shift+Delete → Cookies).

---

## 3. Matriks Hak Akses

### Legenda
- ✅ = Akses penuh (CRUD)
- 👁 = Hanya lihat (Read Only)
- ❌ = Tidak bisa akses

| Fitur | super_admin | akademik | pamong |
|-------|:-----------:|:--------:|:------:|
| **Dashboard** | ✅ (semua data) | ✅ (tanpa SPP & uang saku) | ✅ (tanpa SPP) |
| **Data Santri** | ✅ | ✅ | 👁 |
| **Kelas & Kelompok** | ✅ | ✅ | ❌ |
| **Kenaikan Kelas** | ✅ | ✅ | ❌ |
| **Kegiatan** | ✅ | ✅ | 👁 |
| **Jadwal Kegiatan** | ✅ | ✅ | 👁 |
| **Absensi Kegiatan** | ✅ | ✅ | ✅ |
| **Kartu RFID** | ✅ | ✅ | ❌ |
| **Capaian Santri** | ✅ | ✅ | ✅ |
| **Materi & Semester** | ✅ | ✅ | ❌ |
| **Pelanggaran** | ✅ | ✅ | ❌ |
| **Pembinaan & Sanksi** | ✅ | ✅ | ❌ |
| **Berita** | ✅ | ✅ | ❌ |
| **Kategori Kegiatan** | ✅ | ✅ | ❌ |
| **Rekap Absensi** | ✅ | ✅ | ❌ |
| **Riwayat Kegiatan** | ✅ | ✅ | ❌ |
| **Laporan Kegiatan** | ✅ | ✅ | ❌ |
| **Kesehatan Santri** | ✅ | ✅ | ✅ |
| **Kepulangan** | ✅ | ✅ | ✅ |
| **Uang Saku** | ✅ | ❌ | ✅ |
| **Keuangan Pondok** | ✅ | ❌ | ❌ |
| **Pembayaran SPP** | ✅ | ❌ | ❌ |
| **Manajemen User (santri/wali)** | ✅ | ❌ | ❌ |
| **Manajemen Akun Admin** | ✅ | ❌ | ❌ |

---

## 4. File yang Dimodifikasi / Dibuat

### Migration
| File | Status |
|------|--------|
| `database/migrations/2026_02_24_000001_update_users_role_enum.php` | **BARU** — Migrasi enum role |

### Model
| File | Perubahan |
|------|-----------|
| `app/Models/User.php` | Tambah method: `isSuperAdmin()`, `isAkademik()`, `isPamong()`, `hasRole(...$roles)`. Update `isAdmin()` |

### Middleware
| File | Perubahan |
|------|-----------|
| `app/Http/Middleware/Role.php` | **FIX KRITIS**: Signature `string $roles` → `string ...$roles` (variadic). Hapus `explode()`. Redirect by role. |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | Redirect sesuai role (admin→admin.dashboard, santri→santri.dashboard) |
| `app/Http/Middleware/Authenticate.php` | Dibersihkan (debug log dihapus) |
| `app/Http/Kernel.php` | Disable `AuthenticateSession` dan `ClearStuckSession` dari web middleware group |

### Controller
| File | Perubahan |
|------|-----------|
| `app/Http/Controllers/Auth/AdminAuthController.php` | Register default = `super_admin`. Hapus session invalidation sebelum login. |
| `app/Http/Controllers/DashboardController.php` | Data dashboard kondisional per role. Fix nama kolom uang_saku. |
| `app/Http/Controllers/Admin/UserController.php` | Tambah 6 method CRUD untuk akun admin (akademik/pamong) |

### Routes
| File | Perubahan |
|------|-----------|
| `routes/web.php` | Restrukturisasi menjadi 5 middleware group berdasarkan role |

### Views
| File | Status |
|------|--------|
| `resources/views/layouts/admin-sidebar.blade.php` | **REWRITE** — Menu kondisional per role |
| `resources/views/admin/dashboardAdmin.blade.php` | Update — Seksi SPP/uang saku kondisional |
| `resources/views/admin/dashboard/_kpi-cards.blade.php` | Update — KPI "Belum Ada Wali" hanya super_admin |
| `resources/views/admin/dashboard/_alert-panel.blade.php` | Update — Alert SPP hanya super_admin |
| `resources/views/layouts/app.blade.php` | Update — `isAdmin()` menggantikan `role === 'admin'` |
| `resources/views/admin/users/admin_accounts.blade.php` | **BARU** — Daftar akun admin |
| `resources/views/admin/users/admin_form.blade.php` | **BARU** — Form create/edit akun admin |

---

## 5. Langkah-Langkah yang Dilakukan

### Langkah 1: Migrasi Database
```bash
php artisan migrate
```
Migrasi mengubah enum `role` di tabel `users`:
- **Sebelum:** `admin`, `santri`, `wali`
- **Sesudah:** `super_admin`, `akademik`, `pamong`, `santri`, `wali`
- Semua user yang sebelumnya `admin` otomatis menjadi `super_admin`.

### Langkah 2: Update Model User
Ditambahkan helper method di `User.php`:
```php
public function isSuperAdmin() { return $this->role === 'super_admin'; }
public function isAkademik()   { return $this->role === 'akademik'; }
public function isPamong()     { return $this->role === 'pamong'; }
public function isAdmin()      { return in_array($this->role, ['super_admin', 'akademik', 'pamong']); }
public function hasRole()      { return in_array($this->role, func_get_args()); }
```

### Langkah 3: Fix Middleware Role (Variadic Parameter)
**Root cause** dari redirect loop: Laravel memanggil middleware `role:super_admin,akademik,pamong` dengan 3 argumen terpisah, bukan 1 string. Signature harus menggunakan **variadic** (`...`):

```php
// SALAH (hanya tangkap argumen pertama):
public function handle(Request $request, Closure $next, string $roles)

// BENAR (tangkap semua argumen):
public function handle(Request $request, Closure $next, string ...$roles)
```

### Langkah 4: Restrukturisasi Routes
`routes/web.php` dibagi menjadi 5 middleware group:

| Group | Middleware | Isi |
|-------|-----------|-----|
| 1 | `role:super_admin,akademik,pamong` | Dashboard, Logout |
| 2 | `role:super_admin` | Keuangan, SPP, Manajemen User |
| 3 | `role:super_admin,akademik` | Santri CUD, Kelas, Kegiatan CUD, Pelanggaran, Berita, dll |
| 4 | `role:super_admin,akademik,pamong` | Santri Read, Kegiatan Read, Absensi, Capaian, Kesehatan, Kepulangan |
| 5 | `role:super_admin,pamong` | Uang Saku |

### Langkah 5: Update Sidebar & Dashboard
- Sidebar menampilkan menu sesuai role user yang login
- Dashboard menampilkan data sesuai hak akses role

### Langkah 6: CRUD Akun Admin
Super admin dapat membuat akun akademik/pamong via UI:
- **URL:** `/admin/users/admin`
- Hanya super_admin yang bisa mengakses
- Tidak bisa membuat akun super_admin baru via UI (untuk keamanan)

---

## 6. Cara Membuat Akun Admin Baru

### Via UI (Recommended)
1. Login sebagai **super_admin**
2. Buka menu **Data Master → Akun Admin**
3. Klik **Tambah Akun Admin**
4. Isi form (email, nama, password, pilih role akademik/pamong)
5. Klik **Simpan**

### Via Tinker (Manual)
```bash
php artisan tinker
```
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Nama User',
    'email' => 'user@example.com',
    'username' => 'user@example.com',
    'password' => Hash::make('password123'),
    'role' => 'akademik', // atau 'pamong'
]);
```

---

## 7. Troubleshooting

### Redirect Loop (ERR_TOO_MANY_REDIRECTS)
1. **Bersihkan cookies browser** (Ctrl+Shift+Delete → Cookies)
2. Jalankan:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```
3. Hapus session files:
   ```bash
   # Di PowerShell:
   Remove-Item storage/framework/sessions/* -Force
   ```

### Error 500 Setelah Login
- Periksa `storage/logs/laravel.log` untuk detail error
- Pastikan semua migrasi sudah dijalankan: `php artisan migrate:status`

### User Tidak Bisa Login
- Pastikan kolom `username` terisi (login menggunakan `username`, bukan `email`)
- Untuk update username yang kosong:
  ```bash
  php artisan tinker
  ```
  ```php
  User::whereNull('username')->orWhere('username', '')->get()->each(fn($u) => $u->update(['username' => $u->email]));
  ```

---

## 8. Arsitektur Middleware

```
Request
  │
  ├─ web middleware group (Kernel.php)
  │    ├─ EncryptCookies
  │    ├─ AddQueuedCookiesToResponse
  │    ├─ StartSession
  │    ├─ ShareErrorsFromSession
  │    ├─ VerifyCsrfToken
  │    └─ SubstituteBindings
  │
  ├─ auth middleware (Authenticate.php)
  │    └─ Redirect ke /admin/login jika belum login
  │
  └─ role middleware (Role.php)
       └─ Cek apakah user->role termasuk dalam daftar yang diizinkan
            ├─ Ya  → lanjut ke controller
            └─ Tidak → redirect ke dashboard dengan pesan error
```

> **Catatan:** `AuthenticateSession` dan `ClearStuckSession` telah di-disable dari web middleware group karena menyebabkan konflik session.
