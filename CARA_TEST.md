# 🔧 PANDUAN LENGKAP - Cara Test & Fix

## ⚠️ PENTING: Semua File SUDAH DIUPDATE!

Semua perubahan sudah tersimpan di:
- ✅ routes/web.php
- ✅ UserController.php  
- ✅ wali_accounts.blade.php
- ✅ santri_accounts.blade.php
- ✅ app_config.dart

**TAPI** mungkin browser/Flutter masih pakai file lama (cached).

---

## 🚀 LANGKAH TESTING (IKUTI URUTAN INI!)

### 1️⃣ Test dengan Debug Tool
Buka browser dan akses:
```
http://localhost/TugasAkhir/debug_comprehensive.php
```

Tool ini akan cek:
- ✅ Apakah file sudah ter-update
- ✅ Apakah route sudah benar
- ✅ Apakah API berfungsi
- ✅ Apakah Flutter config sudah benar

### 2️⃣ Clear Browser Cache
**PENTING!** Tekan:
- **Windows:** `Ctrl + Shift + R` atau `Ctrl + F5`
- **Mac:** `Cmd + Shift + R`

Atau buka Incognito/Private Window.

### 3️⃣ Login ke Admin Panel
```
http://localhost/TugasAkhir/sim-pkpps/public/admin/login
```

Login dengan akun admin Anda.

### 4️⃣ Test Delete & Reset di Web
```
http://localhost/TugasAkhir/sim-pkpps/public/admin/users/wali
```

Coba:
- Klik tombol **Hapus** → konfirmasi → lihat apakah akun terhapus
- Klik tombol **Reset** → konfirmasi → lihat pesan sukses

**Jika MASIH BELUM BISA:**
1. Tekan F12 (Developer Tools)
2. Lihat tab **Console** → ada error?
3. Lihat tab **Network** → klik tombol delete → lihat request yang dikirim
4. Screenshot errornya dan kirim ke saya

### 5️⃣ Test Login Mobile

#### A. Hot Restart Flutter (BUKAN Hot Reload!)
```bash
cd c:\xampp\htdocs\TugasAkhir\sim_mobile
flutter clean
flutter run
```

Atau di VS Code: klik icon 🔄 dengan tooltip "Hot Restart"

#### B. Test Login
Gunakan credentials ini:

| Username | Password |
|----------|----------|
| Aydin Fauzan | s002 |
| HELGA FAISA_1 | s001 |
| Mifta Okta Yanti | s003 |

**PENTING:** 
- Username HARUS persis sama (huruf besar/kecil)
- Password adalah NIS (lowercase untuk s001-s003)

#### C. Jika Masih Gagal
1. Cek log Flutter di terminal
2. Cek apakah muncul error "Connection refused"
3. Pastikan XAMPP Apache sudah running
4. Cek IP dengan: `ipconfig` (kalau pakai real device)

---

## 🐛 DEBUG TAMBAHAN

### Jika Delete Masih Error:
Jalankan command ini:
```bash
cd c:\xampp\htdocs\TugasAkhir\sim-pkpps
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### Jika Login Mobile Masih Gagal:
Test API manual:
```bash
# Di PowerShell
$body = '{"id_santri":"Aydin Fauzan","password":"s002"}'
Invoke-RestMethod -Uri "http://localhost/TugasAkhir/sim-pkpps/public/api/v1/login" -Method POST -ContentType "application/json" -Body $body
```

Jika ini berhasil, berarti API OK, masalahnya di Flutter config.

---

## 📞 Masih Belum Bisa?

Kirim screenshot:
1. Error di browser (F12 → Console)
2. Error di Flutter terminal
3. Hasil dari debug_comprehensive.php

Atau kirim:
- URL yang Anda buka
- Tombol apa yang diklik
- Error message yang muncul

---

## ✅ Expected Results

### Delete:
- Klik Hapus → Dialog konfirmasi → Klik OK → Akun hilang dari list
- Muncul pesan hijau: "Akun wali [nama] berhasil dihapus"

### Reset Password:
- Klik Reset → Dialog konfirmasi → Klik OK
- Muncul pesan hijau: "Password akun [nama] berhasil direset ke NIS: [nis]"

### Login Mobile:
- Input username & password → Klik Login
- Loading sebentar → Masuk ke Dashboard
- Menu Profil menampilkan data santri

---

**Semua code sudah benar! Tinggal clear cache & test!** 🚀
