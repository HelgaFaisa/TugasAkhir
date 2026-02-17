# 📰 DOKUMENTASI FITUR BERITA - 3 KATEGORI TARGET

## 🎯 Cara Kerja Fitur Berita

Sistem berita memiliki **3 kategori target** yang menentukan siapa yang bisa melihat berita:

### 1️⃣ **SEMUA SANTRI** (`target_berita = 'semua'`)
- **Siapa yang bisa lihat?** Semua santri yang login ke mobile app
- **Kapan digunakan?** Untuk pengumuman umum, berita penting untuk semua santri
- **Contoh:** Pengumuman libur, jadwal ujian, informasi umum pondok

### 2️⃣ **KELAS TERTENTU** (`target_berita = 'kelas_tertentu'`)
- **Siapa yang bisa lihat?** Hanya santri dari kelas yang dipilih
- **Field yang digunakan:** `target_kelas` (JSON array, contoh: `["PB", "Lambatan"]`)
- **Kapan digunakan?** Untuk pengumuman khusus satu atau beberapa kelas
- **Contoh:** Jadwal kegiatan kelas PB, tugas untuk kelas Cepatan

### 3️⃣ **SANTRI TERTENTU** (`target_berita = 'santri_tertentu'`)
- **Siapa yang bisa lihat?** Hanya santri yang dipilih secara spesifik
- **Relasi:** Menggunakan pivot table `berita_santri`
- **Fitur tambahan:** Bisa tracking status "sudah dibaca" atau "belum dibaca"
- **Kapan digunakan?** Untuk pesan personal, reminder individual
- **Contoh:** Panggilan khusus, informasi pembayaran tertunggak, pemberitahuan pribadi

---

## 🔧 Struktur Database

### Table: `berita`
```sql
CREATE TABLE berita (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_berita VARCHAR(10) UNIQUE,         -- B001, B002, ...
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    penulis VARCHAR(255),
    gambar VARCHAR(255),                   -- Path ke storage
    status ENUM('draft', 'published'),     -- Draft tidak muncul di mobile
    target_berita ENUM('semua', 'kelas_tertentu', 'santri_tertentu'),
    target_kelas JSON,                     -- ["PB", "Lambatan", "Cepatan"]
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table: `berita_santri` (Pivot - untuk santri_tertentu)
```sql
CREATE TABLE berita_santri (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_berita VARCHAR(10),                 -- FK ke berita.id_berita
    id_santri VARCHAR(10),                 -- FK ke santris.id_santri
    sudah_dibaca BOOLEAN DEFAULT FALSE,
    tanggal_baca TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_berita) REFERENCES berita(id_berita),
    FOREIGN KEY (id_santri) REFERENCES santris(id_santri)
);
```

---

## 🚀 Alur Kerja Backend API

### Endpoint: `GET /api/v1/berita`

**Filter Logic (di `ApiBeritaController.php`):**
```php
$query = Berita::where('status', 'published')
    ->where(function($q) use ($idSantri, $santri) {
        // 1. Berita untuk SEMUA
        $q->where('target_berita', 'semua')
        
        // 2. Berita untuk KELAS TERTENTU (cek kelas santri)
        ->orWhere(function($subQ) use ($santri) {
            $subQ->where('target_berita', 'kelas_tertentu')
                 ->whereJsonContains('target_kelas', $santri->kelas);
        })
        
        // 3. Berita untuk SANTRI TERTENTU (cek pivot)
        ->orWhere(function($subQ) use ($idSantri) {
            $subQ->where('target_berita', 'santri_tertentu')
                 ->whereHas('santriTertentu', function($pivot) use ($idSantri) {
                     $pivot->where('id_santri', $idSantri);
                 });
        });
    })
    ->orderBy('created_at', 'desc');
```

**Response Format:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "id_berita": "B001",
      "judul": "Pengumuman Libur",
      "konten": "...",
      "penulis": "Admin",
      "gambar_url": "http://localhost/storage/berita/image.jpg",
      "target_berita": "semua",
      "tanggal": "05 Feb 2026",
      "tanggal_lengkap": "05 February 2026, 10:30",
      "sudah_dibaca": false,
      "tanggal_baca": null
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "total": 25
  }
}
```

---

## 📱 Implementasi Mobile (Flutter)

### API Service (`api_service.dart`):
```dart
Future<Map<String, dynamic>> getBerita({int page = 1}) async {
  final response = await http.get(
    Uri.parse('${AppConfig.baseUrl}/berita?page=$page'),
    headers: await _headers(needsAuth: true),  // ✅ Token diperlukan
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body);
  }
  return {'success': false};
}
```

### UI (`berita_page.dart`):
- Menampilkan list berita yang sudah di-filter oleh backend
- Badge "BARU" untuk berita belum dibaca (khusus `santri_tertentu`)
- Pull-to-refresh untuk update data
- Load more pagination

---

## ✅ CHECKLIST TROUBLESHOOTING

### ❌ **Berita Tidak Muncul di Mobile?**

#### 1. **Cek Database - Ada Berita Published?**
```sql
SELECT id_berita, judul, status, target_berita, target_kelas 
FROM berita 
WHERE status = 'published';
```
- ❌ Jika kosong → **Buat berita baru dan set status 'published'**
- ❌ Jika status 'draft' → **Berita tidak akan muncul di mobile**

#### 2. **Cek Target Berita**

**Untuk target 'semua':**
- ✅ Otomatis muncul untuk semua santri yang login

**Untuk target 'kelas_tertentu':**
```sql
SELECT id_berita, judul, target_kelas 
FROM berita 
WHERE target_berita = 'kelas_tertentu';
```
- ✅ Pastikan `target_kelas` berisi JSON array: `["PB"]`, `["Lambatan", "Cepatan"]`
- ✅ Cek kelas santri yang login cocok dengan `target_kelas`

**Untuk target 'santri_tertentu':**
```sql
SELECT bs.*, b.judul, s.nama_lengkap
FROM berita_santri bs
JOIN berita b ON bs.id_berita = b.id_berita
JOIN santris s ON bs.id_santri = s.id_santri
WHERE b.status = 'published';
```
- ✅ Pastikan ada data di pivot table `berita_santri`
- ✅ Pastikan `id_santri` sesuai dengan santri yang login

#### 3. **Cek User Login & Role**
```sql
SELECT u.id, u.username, u.role, u.role_id, s.nama_lengkap, s.kelas
FROM users u
LEFT JOIN santris s ON u.role_id = s.id_santri
WHERE u.role = 'wali';
```
- ✅ Pastikan user memiliki `role = 'wali'`
- ✅ Pastikan `role_id` terisi dengan `id_santri` yang valid
- ✅ Pastikan santri dengan `id_santri` tersebut ada dan statusnya 'Aktif'

#### 4. **Cek API Response**

**Test di browser/Postman:**
```
GET http://localhost/TugasAkhir/sim-pkpps/public/api/v1/berita
Header: Authorization: Bearer <token_dari_login>
```

Response yang benar:
```json
{
  "success": true,
  "data": [...]  // Array berisi berita
}
```

Response error:
```json
{
  "success": false,
  "message": "Unauthenticated."  // ❌ Token tidak valid/expired
}
```

#### 5. **Cek Mobile App (Flutter Debug Console)**

Setelah login dan buka halaman Berita, lihat console:
```
🔵 GET BERITA URL: http://...
🔵 Berita Response Status: 200
🔵 Berita Response Body: {"success":true,"data":[...]}
✅ Berita berhasil dimuat: 5 item
```

Error yang mungkin:
```
🔴 Berita SocketException           → Server tidak jalan
🔴 Berita error: 401                → Token tidak valid
🔴 Berita Error: FormatException   → Response bukan JSON valid
```

---

## 🛠️ CARA MEMBUAT BERITA BARU

### Via Admin Web (Laravel):

1. **Login ke Admin Panel**
   ```
   http://localhost/TugasAkhir/sim-pkpps/public/login
   ```

2. **Buka Menu Berita → Tambah Berita**

3. **Isi Form:**
   - **Judul:** Judul berita yang menarik
   - **Konten:** Isi berita lengkap
   - **Penulis:** Nama penulis/admin
   - **Gambar:** (Optional) Upload gambar
   - **Status:** Pilih **"Published"** agar muncul di mobile
   - **Target Berita:**
     - **Semua Santri** → Semua bisa lihat
     - **Kelas Tertentu** → Pilih kelas (bisa lebih dari 1)
     - **Santri Tertentu** → Pilih santri spesifik (bisa lebih dari 1)

4. **Simpan**

### Via SQL (Quick Test):

**Berita untuk SEMUA santri:**
```sql
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, created_at, updated_at)
VALUES ('B001', 'Pengumuman Libur', 'Pondok libur tanggal 10-15 Februari 2026', 'Admin', 'published', 'semua', NOW(), NOW());
```

**Berita untuk KELAS PB:**
```sql
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, target_kelas, created_at, updated_at)
VALUES ('B002', 'Jadwal Kelas PB', 'Kegiatan kelas PB dimulai jam 08:00', 'Admin', 'published', 'kelas_tertentu', '["PB"]', NOW(), NOW());
```

**Berita untuk SANTRI TERTENTU (2 steps):**
```sql
-- Step 1: Buat berita
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, created_at, updated_at)
VALUES ('B003', 'Pesan Khusus', 'Harap menemui admin', 'Admin', 'published', 'santri_tertentu', NOW(), NOW());

-- Step 2: Tambah ke pivot table
INSERT INTO berita_santri (id_berita, id_santri, sudah_dibaca, created_at, updated_at)
VALUES ('B003', 'S001', FALSE, NOW(), NOW());  -- Ganti S001 dengan id_santri yang sesuai
```

---

## 🧪 FILE TESTING

Gunakan file `test_api_berita.php` untuk debugging:
```
http://localhost/TugasAkhir/test_api_berita.php
```

File ini akan menampilkan:
1. ✅ Semua berita di database
2. ✅ Sample data santri
3. ✅ Pivot table berita_santri
4. ✅ Data user/wali
5. ✅ Simulasi filter berita untuk santri tertentu

---

## 📊 CONTOH SKENARIO

### Skenario 1: Pengumuman Umum
- **Target:** Semua Santri
- **Contoh:** "Libur Pondok 10-15 Februari"
- **Setting:** `target_berita = 'semua'`
- **Result:** Semua santri yang login bisa lihat

### Skenario 2: Info Kelas
- **Target:** Kelas PB dan Lambatan
- **Contoh:** "Jadwal Ujian Kelas PB & Lambatan"
- **Setting:** `target_berita = 'kelas_tertentu'`, `target_kelas = ["PB", "Lambatan"]`
- **Result:** Hanya santri kelas PB dan Lambatan yang bisa lihat

### Skenario 3: Pesan Personal
- **Target:** Santri Ahmad (S001) dan Budi (S002)
- **Contoh:** "Harap menemui admin untuk pengecekan kesehatan"
- **Setting:** `target_berita = 'santri_tertentu'`, pivot table isi S001 dan S002
- **Result:** Hanya Ahmad dan Budi yang bisa lihat, dengan badge "BARU" sampai mereka buka

---

## 🎓 KESIMPULAN

Fitur berita dengan 3 kategori target ini memberikan fleksibilitas:
- **Efisien** → Tidak perlu kirim satu-satu
- **Fleksibel** → Bisa target sesuai kebutuhan
- **Trackable** → Bisa tracking siapa yang sudah baca (untuk santri_tertentu)
- **Secure** → Filter di backend, mobile tidak bisa akses berita yang bukan haknya

**Backend sudah benar**, pastikan:
1. ✅ Data berita ada dan status 'published'
2. ✅ Target berita sesuai dengan santri yang login
3. ✅ Token authentication valid
4. ✅ Server Laravel jalan
5. ✅ Koneksi database OK

Jika masih ada masalah, cek console Flutter untuk error spesifik!
