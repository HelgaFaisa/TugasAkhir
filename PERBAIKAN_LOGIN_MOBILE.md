# Panduan Perbaikan Sistem Login Mobile SIM-PKPPS

## ✅ Perbaikan yang Sudah Dilakukan

### 1. **Auto-Fill Username & Password** ✅
- JavaScript diperbaiki dari `@push('scripts')` menjadi inline `<script>` di [create_account.blade.php](sim-pkpps/resources/views/admin/users/create_account.blade.php)
- Saat memilih santri di dropdown, otomatis mengisi:
  - **Username**: Nama Santri
  - **Password**: NIS Santri
- Field menjadi readonly saat sudah terisi otomatis
- Jika santri belum punya NIS, akan muncul alert dan field bisa diisi manual

### 2. **Fungsi Delete Akun** ✅
- Ditambahkan method `destroyAccount()` di [UserController.php](sim-pkpps/app/Http/Controllers/Admin/UserController.php)
- Routes ditambahkan:
  - `DELETE /admin/users/santri/{user}` → `admin.users.santri_destroy`
  - `DELETE /admin/users/wali/{user}` → `admin.users.wali_destroy`
- Tombol delete dengan konfirmasi di:
  - [santri_accounts.blade.php](sim-pkpps/resources/views/admin/users/santri_accounts.blade.php)
  - [wali_accounts.blade.php](sim-pkpps/resources/views/admin/users/wali_accounts.blade.php)

### 3. **Fungsi Reset Password** ✅
- Ditambahkan method `resetPassword()` di [UserController.php](sim-pkpps/app/Http/Controllers/Admin/UserController.php)
- Reset password otomatis ke NIS santri
- Routes ditambahkan:
  - `POST /admin/users/santri/{user}/reset-password` → `admin.users.santri_reset_password`
  - `POST /admin/users/wali/{user}/reset-password` → `admin.users.wali_reset_password`
- Tombol reset dengan konfirmasi di view akun santri/wali

---

## 🔧 Cara Testing Login Mobile

### A. Test API Login Menggunakan File PHP Test
1. **Edit file [test_login.php](test_login.php)**
   ```php
   $username = "Ahmad Fauzi";  // Ganti dengan nama santri yang sudah punya akun wali
   $password = "2024001";      // Ganti dengan NIS santri tersebut
   ```

2. **Jalankan dari terminal:**
   ```bash
   php test_login.php
   ```

3. **Hasil yang diharapkan:**
   ```
   ✅ LOGIN BERHASIL!
   Token: 1|xxxxxxxxxxxxx
   User: Ahmad Fauzi
   Role: wali
   ```

### B. Cek Database Untuk Memastikan Akun Ada
```sql
-- Cek akun wali yang sudah dibuat
SELECT 
    u.id,
    u.username,
    u.role,
    s.nama_lengkap,
    s.nis
FROM users u
JOIN santris s ON u.role_id = s.id_santri
WHERE u.role = 'wali';
```

### C. Troubleshooting Login Mobile Gagal

#### ❌ Error: "Username atau password salah"
**Penyebab:**
- Username tidak match persis dengan database (case-sensitive, spasi, typo)
- Password salah (pastikan menggunakan NIS yang benar)

**Solusi:**
1. Cek username di database:
   ```sql
   SELECT username FROM users WHERE role='wali';
   ```
2. Pastikan di Flutter login menggunakan username yang **PERSIS SAMA** termasuk huruf besar/kecil dan spasi
3. Password harus NIS santri (bisa dicek di tabel santris)

#### ❌ Error: "Connection refused" / "Network error"
**Penyebab:**
- Laravel server tidak jalan
- Base URL salah di Flutter

**Solusi:**
1. Pastikan Laravel server running:
   ```bash
   cd sim-pkpps
   php artisan serve
   ```
2. Cek [app_config.dart](sim_mobile/lib/core/config/app_config.dart):
   ```dart
   static const String baseUrl = 'http://10.0.2.2:8000/api/v1'; // Emulator
   // atau
   static const String baseUrl = 'http://192.168.x.x:8000/api/v1'; // Real device
   ```

#### ❌ Error: "Akun tidak memiliki akses mobile"
**Penyebab:**
- User role bukan 'santri' atau 'wali'

**Solusi:**
- Pastikan di database field `role` adalah 'wali', bukan 'admin' atau lainnya

---

## 📋 Checklist Testing Lengkap

### 1. Testing Web Admin (Buat Akun Wali)
- [ ] Buka halaman Manajemen Akun Wali (`/admin/users/wali`)
- [ ] Klik "Buat Akun Wali"
- [ ] Pilih santri dari dropdown
- [ ] **Cek:** Username otomatis terisi dengan nama santri ✅
- [ ] **Cek:** Password otomatis terisi dengan NIS ✅
- [ ] Klik "Simpan"
- [ ] **Cek:** Akun muncul di daftar dengan info login ✅

### 2. Testing Fungsi Delete
- [ ] Di halaman Manajemen Akun Wali
- [ ] Klik tombol "Hapus" pada salah satu akun
- [ ] **Cek:** Muncul konfirmasi dialog ✅
- [ ] Klik OK
- [ ] **Cek:** Akun terhapus dari daftar ✅

### 3. Testing Fungsi Reset Password
- [ ] Di halaman Manajemen Akun Wali
- [ ] Klik tombol "Reset" pada salah satu akun
- [ ] **Cek:** Muncul konfirmasi dialog ✅
- [ ] Klik OK
- [ ] **Cek:** Muncul pesan sukses dengan info password baru (NIS) ✅

### 4. Testing Login Mobile
- [ ] Jalankan Flutter app (emulator/real device)
- [ ] Pastikan Laravel server running (`php artisan serve`)
- [ ] Di login page, masukkan:
  - **Username**: Nama santri (persis seperti di database)
  - **Password**: NIS santri
- [ ] Klik Login
- [ ] **Cek:** Berhasil masuk ke dashboard ✅
- [ ] **Cek:** Menu Profil menampilkan data santri ✅

---

## 🐛 Debug Mode - Jika Masih Gagal

### 1. Tambahkan Log di ApiAuthController
Edit [ApiAuthController.php](sim-pkpps/app/Http/Controllers/Api/ApiAuthController.php):

```php
public function login(Request $request)
{
    // Log untuk debug
    \Log::info('Login attempt', [
        'username' => $request->id_santri,
        'password_length' => strlen($request->password)
    ]);

    $user = User::where('username', $request->id_santri)->first();
    
    if (!$user) {
        \Log::warning('User not found', ['username' => $request->id_santri]);
    }
    
    // ... kode lainnya
}
```

Cek log di `storage/logs/laravel.log`

### 2. Test Manual Dengan Postman/cURL

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "id_santri": "Ahmad Fauzi",
    "password": "2024001"
  }'
```

### 3. Validasi Data di Database

```sql
-- Cek akun wali yang baru dibuat
SELECT 
    u.id,
    u.username,
    u.role,
    u.role_id,
    s.nama_lengkap,
    s.nis,
    LENGTH(u.password) as password_hash_length
FROM users u
JOIN santris s ON u.role_id = s.id_santri
WHERE u.role = 'wali'
ORDER BY u.id DESC
LIMIT 5;
```

**Password hash length seharusnya 60 karakter (bcrypt)**

---

## 📱 Format Login yang Benar

| Field | Value | Contoh |
|-------|-------|--------|
| Username | Nama Santri (PERSIS seperti di database) | `Ahmad Fauzi` |
| Password | NIS Santri | `2024001` |
| Role | Otomatis terdeteksi dari database | `wali` |

**⚠️ PENTING:**
- Username **case-sensitive**: "Ahmad Fauzi" ≠ "ahmad fauzi"
- Spasi dihitung: "Ahmad Fauzi" ≠ "AhmadFauzi"
- Password adalah NIS **plain text** (tidak di-hash saat input), Laravel akan auto-verify hash

---

## 🔒 Keamanan

- Password di database di-hash dengan bcrypt (60 karakter)
- Token menggunakan Laravel Sanctum
- Setiap login, token lama dihapus (single device per account)
- API hanya bisa diakses oleh role 'santri' dan 'wali'

---

## 📞 Troubleshooting Contact

Jika masih ada masalah:
1. Cek file log Laravel: `sim-pkpps/storage/logs/laravel.log`
2. Cek Flutter console untuk error network
3. Pastikan username & password **100% match** dengan database
4. Test dengan file [test_login.php](test_login.php) terlebih dahulu sebelum test di mobile

---

**Semua fungsi sudah diimplementasikan:**
- ✅ Auto-fill username & password
- ✅ Delete akun
- ✅ Reset password
- ✅ Login mobile ready (tinggal test dengan data yang benar)
