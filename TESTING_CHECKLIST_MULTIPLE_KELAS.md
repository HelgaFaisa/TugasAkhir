# Testing Checklist - Multiple Kelas System

## Overview
Checklist lengkap untuk testing fitur Multiple Kelas pada backend Laravel dan aplikasi mobile Flutter.

---

## 📋 PHASE 1: Backend API Testing

### A. Persiapan Data Testing

#### ✅ Step 1: Cek Database Structure
```sql
-- Cek tabel santri_kelas
DESC santri_kelas;

-- Expected columns:
-- id, id_santri, id_kelas, tahun_ajaran, is_primary, created_at, updated_at
```

#### ✅ Step 2: Insert Sample Data (Manual)
```sql
-- Insert santri dengan multiple kelas
-- Contoh: Santri S001 masuk 4 kelas

-- Kelas 1: PB Putra A (bukan primary)
INSERT INTO santri_kelas (id_santri, id_kelas, tahun_ajaran, is_primary) 
VALUES ('S001', 1, '2025/2026', 0);

-- Kelas 2: Lambatan B (PRIMARY)
INSERT INTO santri_kelas (id_santri, id_kelas, tahun_ajaran, is_primary) 
VALUES ('S001', 5, '2025/2026', 1);

-- Kelas 3: Cepatan A (bukan primary)
INSERT INTO santri_kelas (id_santri, id_kelas, tahun_ajaran, is_primary) 
VALUES ('S001', 8, '2025/2026', 0);

-- Kelas 4: Hadist Pemula (bukan primary)
INSERT INTO santri_kelas (id_santri, id_kelas, tahun_ajaran, is_primary) 
VALUES ('S001', 15, '2025/2026', 0);
```

#### ✅ Step 3: Verify Sample Data
```sql
-- Cek data santri S001
SELECT sk.*, k.nama_kelas, kk.nama_kelompok
FROM santri_kelas sk
JOIN kelas k ON sk.id_kelas = k.id
JOIN kelompok_kelas kk ON k.id_kelompok = kk.id_kelompok
WHERE sk.id_santri = 'S001';

-- Expected: 4 rows, 1 dengan is_primary = 1
```

---

### B. Testing Login Endpoint

#### ✅ Test 1: Login Berhasil
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "id_santri": "S001",
    "password": "password123"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "token": "1|abc123xyz...",
  "user": {
    "name": "Ahmad Santoso",
    "role": "santri",
    "role_id": "S001"
  },
  "santri": {
    "id_santri": "S001",
    ...
    "kelas": "Lambatan B",
    "kelas_list": [ ... ]
  }
}
```

**Checklist:**
- [ ] Response status: 200 OK
- [ ] Field `token` ada dan valid
- [ ] Field `santri.kelas` = "Lambatan B" (primary)
- [ ] Field `santri.kelas_list` adalah array
- [ ] `kelas_list` punya 4 kelompok (atau sesuai data)
- [ ] Ada 1 kelas dengan `is_primary = true`

#### ✅ Test 2: Login Gagal (Password Salah)
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "id_santri": "S001",
    "password": "wrongpassword"
  }'
```

**Expected Response:**
```json
{
  "message": "ID Santri atau password salah.",
  "errors": {
    "id_santri": ["ID Santri atau password salah."]
  }
}
```

**Checklist:**
- [ ] Response status: 422 Unprocessable Entity
- [ ] Error message jelas

---

### C. Testing Profile Endpoint

#### ✅ Test 3: Get Profile (With Valid Token)
```bash
# Ganti YOUR_TOKEN dengan token dari login
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "id_santri": "S001",
    "nis": "2024001",
    "nama_lengkap": "Ahmad Santoso",
    "jenis_kelamin": "Laki-laki",
    "status": "Aktif",
    "kelas": "Lambatan B",
    "kelas_list": [
      {
        "kelompok_id": "KLMPK001",
        "kelompok_name": "PB",
        "kelas": [
          {
            "id_kelas": 1,
            "kode_kelas": "KLS001",
            "nama_kelas": "PB Putra A",
            "is_primary": false
          }
        ]
      },
      {
        "kelompok_id": "KLMPK002",
        "kelompok_name": "Lambatan",
        "kelas": [
          {
            "id_kelas": 5,
            "kode_kelas": "KLS005",
            "nama_kelas": "Lambatan B",
            "is_primary": true
          }
        ]
      },
      {
        "kelompok_id": "KLMPK003",
        "kelompok_name": "Cepatan",
        "kelas": [
          {
            "id_kelas": 8,
            "kode_kelas": "KLS008",
            "nama_kelas": "Cepatan A",
            "is_primary": false
          }
        ]
      },
      {
        "kelompok_id": "KLMPK004",
        "kelompok_name": "Hadist",
        "kelas": [
          {
            "id_kelas": 15,
            "kode_kelas": "KLS015",
            "nama_kelas": "Hadist Pemula",
            "is_primary": false
          }
        ]
      }
    ],
    ...
  }
}
```

**Checklist:**
- [ ] Response status: 200 OK
- [ ] Field `kelas` ada (string)
- [ ] Field `kelas_list` ada (array)
- [ ] Setiap kelompok punya struktur benar
- [ ] Primary kelas punya `is_primary: true`
- [ ] Kelompok di-group dengan benar

#### ✅ Test 4: Get Profile (Without Token)
```bash
curl -X GET http://localhost:8000/api/profile
```

**Expected Response:**
```json
{
  "message": "Unauthenticated."
}
```

**Checklist:**
- [ ] Response status: 401 Unauthorized

---

### D. Edge Case Testing

#### ✅ Test 5: Santri Tanpa Kelas
```sql
-- Hapus semua kelas santri S002 (untuk testing)
DELETE FROM santri_kelas WHERE id_santri = 'S002';
```

```bash
# Login sebagai S002
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"id_santri": "S002", "password": "password123"}'
```

**Expected:**
```json
{
  "santri": {
    "kelas": "Belum Ada Kelas",
    "kelas_list": []
  }
}
```

**Checklist:**
- [ ] Field `kelas` = "Belum Ada Kelas"
- [ ] Field `kelas_list` = [] (empty array)
- [ ] No error/crash

#### ✅ Test 6: Santri dengan 1 Kelas Saja
```sql
-- Insert 1 kelas untuk S003
INSERT INTO santri_kelas (id_santri, id_kelas, tahun_ajaran, is_primary) 
VALUES ('S003', 1, '2025/2026', 1);
```

```bash
# Login sebagai S003
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"id_santri": "S003", "password": "password123"}'
```

**Expected:**
```json
{
  "santri": {
    "kelas": "PB Putra A",
    "kelas_list": [
      {
        "kelompok_id": "KLMPK001",
        "kelompok_name": "PB",
        "kelas": [
          {
            "id_kelas": 1,
            "nama_kelas": "PB Putra A",
            "is_primary": true
          }
        ]
      }
    ]
  }
}
```

**Checklist:**
- [ ] Field `kelas` = nama kelas
- [ ] `kelas_list` punya 1 item
- [ ] `is_primary` = true

#### ✅ Test 7: Santri Tanpa Primary (Semua is_primary = false)
```sql
-- Update S004: semua kelas jadi non-primary
UPDATE santri_kelas SET is_primary = 0 WHERE id_santri = 'S004';
```

```bash
# Login sebagai S004
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"id_santri": "S004", "password": "password123"}'
```

**Expected:**
```json
{
  "santri": {
    "kelas": "PB Putra A",  // Fallback ke kelas pertama
    "kelas_list": [ ... ],  // Semua is_primary: false
  }
}
```

**Checklist:**
- [ ] Field `kelas` = nama kelas pertama (fallback)
- [ ] Semua item di `kelas_list` punya `is_primary: false`
- [ ] No error/crash

---

### E. Performance Testing

#### ✅ Test 8: Query Count (N+1 Problem Check)
```php
// Tambahkan di ApiAuthController.php (temporary)
\DB::enableQueryLog();

// ... existing code ...

\Log::info('Query count: ' . count(\DB::getQueryLog()));
\Log::info('Queries:', \DB::getQueryLog());
```

**Test:**
1. Login sebagai santri dengan 5 kelas
2. Cek `storage/logs/laravel.log`

**Expected:**
- Query count: 2-3 queries (optimal)
- **NO** N+1 problem (banyak query loop)

**Checklist:**
- [ ] Query count < 5
- [ ] Eager loading bekerja (`with()` clause)

#### ✅ Test 9: Response Time
```bash
# Test response time (run 5x)
time curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"id_santri": "S001", "password": "password123"}'
```

**Expected:**
- Average response time: < 500ms
- Max response time: < 1000ms

**Checklist:**
- [ ] Response time acceptable
- [ ] No timeout

#### ✅ Test 10: Response Size
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"id_santri": "S001", "password": "password123"}' \
  --compressed | wc -c
```

**Expected:**
- Response size: < 10KB

**Checklist:**
- [ ] Response size reasonable
- [ ] No unnecessary data

---

## 📱 PHASE 2: Flutter Mobile App Testing

### F. Setup Testing Environment

#### ✅ Step 1: Update API Base URL
```dart
// lib/core/api/api_service.dart
static const String _baseUrl = 'http://YOUR_LOCAL_IP:8000/api';

// Contoh:
// Windows: 'http://192.168.1.100:8000/api'
// Mac: 'http://192.168.1.100:8000/api'
```

#### ✅ Step 2: Build & Run App
```bash
cd sim_mobile
flutter clean
flutter pub get
flutter run
```

**Checklist:**
- [ ] App build success
- [ ] No compile errors
- [ ] App launched on device/emulator

---

### G. UI Display Testing

#### ✅ Test 11: Login & Display Profile
1. Launch app
2. Login dengan S001 (yang punya 4 kelas)
3. Tap tab "Profil"

**Expected:**
- Header:
  - [ ] Avatar displayed
  - [ ] Nama lengkap displayed
  - [ ] ID Santri displayed
  - [ ] **Primary kelas badge** displayed ("📚 Lambatan B")
  - [ ] **Hint "+3 kelas lainnya"** displayed
  - [ ] Status badge displayed ("Aktif")

- Informasi Dasar Card:
  - [ ] No "Kelas" row (removed)
  - [ ] All other fields displayed

- **Kelas yang Diikuti Section:**
  - [ ] Section card displayed
  - [ ] 4 kelompok displayed (PB, Lambatan, Cepatan, Hadist)
  - [ ] Each kelompok has correct color & icon

#### ✅ Test 12: ExpansionTile Interaction
1. Tap "PB" kelompok (saat collapsed)
2. **Expected:** ExpansionTile expands, show "PB Putra A"
3. Tap "PB" lagi
4. **Expected:** ExpansionTile collapses

**Checklist:**
- [ ] Expand animation smooth
- [ ] Collapse animation smooth
- [ ] No lag/jank

#### ✅ Test 13: Primary Badge Display
1. Expand "Lambatan" kelompok
2. **Expected:** "Lambatan B" punya badge "⭐ Utama"
3. Expand "PB" kelompok
4. **Expected:** "PB Putra A" TIDAK punya badge (is_primary: false)

**Checklist:**
- [ ] Badge "⭐ Utama" muncul HANYA di primary kelas
- [ ] Badge styling correct (gold background, white icon/text)

#### ✅ Test 14: Color Coding
1. Cek warna per kelompok:
   - [ ] PB → Blue (#3b82f6)
   - [ ] Lambatan → Orange (#fb923c)
   - [ ] Cepatan → Green (#10b981)
   - [ ] Hadist → Teal (#14b8a6)

**Checklist:**
- [ ] Icon badge color match
- [ ] Border color match
- [ ] Primary kelas highlight match

---

### H. Edge Case UI Testing

#### ✅ Test 15: Santri Tanpa Kelas
1. Login sebagai S002 (tanpa kelas)
2. Tap tab "Profil"

**Expected:**
- [ ] Header: Primary badge show "-" atau "Belum Ada Kelas"
- [ ] NO hint "+X kelas lainnya"
- [ ] Section "Kelas yang Diikuti" **TIDAK MUNCUL**
- [ ] Informasi Dasar, Alamat, Orang Tua tetap displayed

#### ✅ Test 16: Santri dengan 1 Kelas
1. Login sebagai S003 (1 kelas: PB Putra A)
2. Tap tab "Profil"

**Expected:**
- [ ] Header: Primary badge show "PB Putra A"
- [ ] NO hint "+X kelas lainnya" (karena cuma 1)
- [ ] Section "Kelas yang Diikuti" displayed
- [ ] 1 kelompok saja (PB)
- [ ] Badge "⭐ Utama" muncul

#### ✅ Test 17: Network Error Handling
1. Disconnect internet/WiFi
2. Login atau pull-to-refresh

**Expected:**
- [ ] Error message displayed
- [ ] App tidak crash
- [ ] Cached data (jika ada) tetap displayed

---

### I. Responsive & Performance Testing

#### ✅ Test 18: Small Screen (iPhone SE, 320px)
1. Test di iPhone SE atau emulator 320px width
2. Scroll semua section

**Expected:**
- [ ] No horizontal overflow
- [ ] Text tidak terpotong (ellipsis bekerja)
- [ ] Padding proporsional
- [ ] Badge "Utama" tidak keluar container

#### ✅ Test 19: Large Screen (iPad, 800px)
1. Test di iPad atau emulator tablet
2. Scroll semua section

**Expected:**
- [ ] Layout rapi
- [ ] Padding tidak terlalu besar/kecil
- [ ] ExpansionTile width reasonable

#### ✅ Test 20: Pull-to-Refresh
1. Di profil page, swipe down
2. **Expected:** Loading indicator muncul
3. Wait 1-2 detik
4. **Expected:** Data refresh dari API

**Checklist:**
- [ ] Loading indicator displayed
- [ ] Data updated
- [ ] No crash

#### ✅ Test 21: Memory & Performance
1. Open Developer Tools / Profiler
2. Navigate antara tab (Beranda → Profil → dll)
3. Repeat 5x

**Expected:**
- [ ] Memory usage stable (< 100MB)
- [ ] No memory leak
- [ ] Frame rate: 60 FPS
- [ ] No dropped frames

---

## 📊 PHASE 3: Integration Testing

### J. End-to-End Scenario

#### ✅ Test 22: Complete User Flow
**Scenario:** Ahmad seorang santri baru, didaftarkan oleh admin, lalu login pertama kali.

1. **Admin:** Create santri S010
2. **Admin:** Assign 3 kelas (PB, Lambatan primary, Cepatan)
3. **Admin:** Create user account untuk S010
4. **Mobile:** Login dengan S010
5. **Mobile:** Tap tab Profil

**Expected:**
- [ ] Login berhasil
- [ ] Profil displayed dengan 3 kelas
- [ ] Lambatan sebagai primary (badge "Utama")
- [ ] Primary kelas badge di header
- [ ] Hint "+2 kelas lainnya"

#### ✅ Test 23: Admin Update Kelas → Mobile Refresh
**Scenario:** Admin menambah kelas baru untuk santri.

1. **Mobile:** Login S001, lihat profil (4 kelas)
2. **Admin/Web:** Add kelas baru untuk S001 (Tahfidz)
3. **Mobile:** Pull-to-refresh di profil page

**Expected:**
- [ ] Data refresh dari API
- [ ] 5 kelas sekarang displayed
- [ ] Hint "+4 kelas lainnya"
- [ ] Primary kelas tetap sama

#### ✅ Test 24: Change Primary Kelas
**Scenario:** Admin mengubah primary kelas santri.

1. **Mobile:** Login S001, primary = "Lambatan B"
2. **Admin/Web:** Update primary ke "Cepatan A"
3. **Mobile:** Pull-to-refresh

**Expected:**
- [ ] Primary kelas badge di header = "Cepatan A"
- [ ] Badge "⭐ Utama" pindah ke "Cepatan A"
- [ ] "Lambatan B" tidak punya badge lagi

---

## ✅ PHASE 4: Backward Compatibility Testing

### K. Compatibility Testing

#### ✅ Test 25: Old App + New Backend
**Scenario:** User belum update app, tapi backend sudah update.

**Setup:**
1. Deploy backend baru (dengan kelas_list)
2. Use app versi lama (hanya baca field 'kelas')

**Expected:**
- [ ] Login berhasil
- [ ] Field 'kelas' masih ada di response
- [ ] App lama display kelas primary (single)
- [ ] No crash on app lama

#### ✅ Test 26: New App + Old Backend
**Scenario:** User update app, tapi backend belum update.

**Setup:**
1. Use backend lama (belum ada kelas_list)
2. Use app versi baru

**Expected:**
- [ ] Login berhasil
- [ ] `kelas_list` = null atau tidak ada
- [ ] App baru fallback ke field 'kelas'
- [ ] Section "Kelas yang Diikuti" tidak muncul
- [ ] No crash

---

## 📈 Results Summary

### Backend Testing Results
| Test | Status | Notes |
|------|--------|-------|
| Login Endpoint | ☐ Pass ☐ Fail | |
| Profile Endpoint | ☐ Pass ☐ Fail | |
| Empty Kelas | ☐ Pass ☐ Fail | |
| Single Kelas | ☐ Pass ☐ Fail | |
| No Primary | ☐ Pass ☐ Fail | |
| Query Performance | ☐ Pass ☐ Fail | |
| Response Time | ☐ Pass ☐ Fail | |

### Frontend Testing Results
| Test | Status | Notes |
|------|--------|-------|
| UI Display | ☐ Pass ☐ Fail | |
| Expansion Tile | ☐ Pass ☐ Fail | |
| Primary Badge | ☐ Pass ☐ Fail | |
| Color Coding | ☐ Pass ☐ Fail | |
| Empty State | ☐ Pass ☐ Fail | |
| Pull-to-Refresh | ☐ Pass ☐ Fail | |
| Responsive | ☐ Pass ☐ Fail | |

### Integration Testing Results
| Test | Status | Notes |
|------|--------|-------|
| End-to-End Flow | ☐ Pass ☐ Fail | |
| Admin Update | ☐ Pass ☐ Fail | |
| Change Primary | ☐ Pass ☐ Fail | |
| Backward Compat | ☐ Pass ☐ Fail | |

---

## 🐛 Bug Report Template

```markdown
### Bug Title
[Singkat dan jelas]

### Environment
- OS: [Windows/Mac/Linux]
- Backend: Laravel 10.x
- Frontend: Flutter 3.x
- Device: [iPhone 13, Android Pixel, etc.]

### Steps to Reproduce
1. Login sebagai S001
2. Tap tab Profil
3. Expand kelompok "PB"
4. ...

### Expected Behavior
[Apa yang seharusnya terjadi]

### Actual Behavior
[Apa yang benar-benar terjadi]

### Screenshots
[Attach screenshots jika ada]

### Logs
[Laravel log, Flutter console log]
```

---

## ✅ Sign-off

### Backend Testing
- Tester: _______________
- Date: _______________
- Signature: _______________

### Frontend Testing
- Tester: _______________
- Date: _______________
- Signature: _______________

### Integration Testing
- Tester: _______________
- Date: _______________
- Signature: _______________

---

## 📞 Contact

Jika ada masalah, refer ke:
- `MULTIPLE_KELAS_API_RESPONSE.md` - API documentation
- `MULTIPLE_KELAS_UI_FLUTTER.md` - UI documentation
- `storage/logs/laravel.log` - Backend errors
- Flutter DevTools - Frontend debugging
