# 🔧 FIX: "Koneksi Gagal" di Mobile App

## 🎯 Masalah
Aplikasi Flutter menampilkan error: **"Koneksi gagal, periksa internet Anda"**

## ✅ Solusi Sudah Diterapkan

File `app_config.dart` sudah diupdate dengan IP komputer Anda: **10.130.244.240**

---

## 📱 LANGKAH-LANGKAH FIX

### 1️⃣ Pastikan Device & Komputer di WiFi yang Sama

**PENTING!** HP dan komputer harus terhubung ke WiFi yang sama.

Cek WiFi:
- Komputer: Lihat icon WiFi di taskbar
- HP: Settings → WiFi → lihat nama network

### 2️⃣ Test Koneksi dari HP

**A. Buka Browser di HP, akses:**
```
http://10.130.244.240/TugasAkhir/test_mobile_api.html
```

**B. Klik tombol:**
- "Test Koneksi Server" → harus muncul ✅ KONEKSI BERHASIL
- "Test Login API" → harus muncul ✅ LOGIN BERHASIL

**Jika halaman tidak bisa dibuka:**
→ Lanjut ke Step 3 (Windows Firewall)

### 3️⃣ Fix Windows Firewall

Windows Firewall mungkin memblokir koneksi dari HP.

**Cara 1: Izinkan Apache (Recommended)**

1. Buka Command Prompt **as Administrator**
2. Jalankan:
```cmd
netsh advfirewall firewall add rule name="Apache HTTP" dir=in action=allow protocol=TCP localport=80
netsh advfirewall firewall add rule name="Apache HTTPS" dir=in action=allow protocol=TCP localport=443
```

**Cara 2: Matikan Firewall Sementara (untuk testing)**

1. Windows Settings → Update & Security → Windows Security
2. Firewall & network protection
3. Private network → Turn off (HANYA untuk testing!)
4. Setelah berhasil, nyalakan lagi dan gunakan Cara 1

### 4️⃣ Restart Flutter App

Setelah test koneksi berhasil:

```bash
cd c:\xampp\htdocs\TugasAkhir\sim_mobile
flutter clean
flutter run
```

**PENTING:** Harus **Hot Restart** (bukan hot reload!)
- VS Code: Klik icon 🔄
- Android Studio: Klik lightning bolt hijau

### 5️⃣ Test Login

**Gunakan credentials:**
- Username: `Aydin Fauzan`
- Password: `s002`

---

## 🐛 Troubleshooting

### Error: "Halaman test_mobile_api.html tidak bisa dibuka"

**Penyebab:** Firewall atau WiFi berbeda

**Solusi:**
1. Ping dari HP ke komputer:
   - Install app "Network Utilities" atau "Fing"
   - Ping ke: 10.130.244.240
   - Jika timeout → WiFi berbeda atau Firewall

2. Cek XAMPP Apache:
   - Buka XAMPP Control Panel
   - Pastikan Apache **running** (hijau)

### Error: "Test koneksi berhasil, tapi login gagal"

**Penyebab:** API atau database bermasalah

**Solusi:**
Test dari komputer dulu:
```bash
$body = '{"id_santri":"Aydin Fauzan","password":"s002"}'
Invoke-RestMethod -Uri "http://localhost/TugasAkhir/sim-pkpps/public/api/v1/login" -Method POST -ContentType "application/json" -Body $body
```

Jika ini gagal:
- Cek routes: `php artisan route:list --name=login`
- Cek database connection
- Cek Laravel log: `sim-pkpps/storage/logs/laravel.log`

### Error: "Flutter masih error 'koneksi gagal'"

**Penyebab:** Config tidak ter-reload

**Solusi:**
1. Stop Flutter app (Shift+F5)
2. Jalankan:
   ```bash
   flutter clean
   flutter pub get
   flutter run
   ```
3. Atau uninstall app dari HP, install ulang

---

## 🔍 Cek IP Komputer Berubah

Jika IP komputer berubah (setelah restart/ganti WiFi):

1. Cek IP baru:
   ```bash
   ipconfig | findstr IPv4
   ```

2. Update `app_config.dart`:
   ```dart
   static const String baseUrl = 'http://[IP_BARU]/TugasAkhir/sim-pkpps/public/api/v1';
   ```

3. Hot restart Flutter

---

## ✅ Checklist Final

- [ ] HP dan komputer di WiFi yang sama
- [ ] XAMPP Apache running
- [ ] Firewall rule untuk Apache sudah dibuat
- [ ] Test koneksi dari HP berhasil (test_mobile_api.html)
- [ ] Flutter app sudah hot restart
- [ ] Login dengan username & password yang benar

---

## 📞 Masih Error?

Kirim screenshot:
1. Error di Flutter (terminal log)
2. Hasil test dari test_mobile_api.html
3. XAMPP Control Panel (Apache status)
4. WiFi settings (HP dan komputer)

---

**IP Komputer Anda: 10.130.244.240**
**Test Page: http://10.130.244.240/TugasAkhir/test_mobile_api.html**
