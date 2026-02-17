# PANDUAN MIGRASI SISTEM KELAS BARU

## Ringkasan

Migrasi dari kolom `kelas` hardcoded (PB, Lambatan, Cepatan) di tabel `santris` ke sistem relasional baru menggunakan tabel `santri_kelas`, `kelas`, dan `kelompok_kelas`.

## Prasyarat

Pastikan tabel berikut sudah ada dan terisi data:
- `kelompok_kelas` — minimal 3 kelompok (PB, Lambatan, Cepatan)
- `kelas` — minimal 1 kelas aktif per kelompok

Cek via tinker:
```bash
cd sim-pkpps
php artisan tinker
>>> App\Models\KelompokKelas::active()->count()  # harus >= 3
>>> App\Models\Kelas::active()->count()            # harus >= 3
```

---

## Urutan Eksekusi (Step-by-Step)

### TAHAP 1: Migrasi Data (Kolom Lama → Tabel Baru)

```bash
cd sim-pkpps

# 1. Preview dulu (dry-run) — TIDAK mengubah database
php artisan migrate:santri-kelas-full --dry-run

# 2. Periksa output, pastikan mapping benar:
#    PB       → KLS00x (...)
#    Lambatan → KLS00x (...)
#    Cepatan  → KLS00x (...)

# 3. Execute migrasi data (real)
php artisan migrate:santri-kelas-full

# 4. Validasi: Periksa tabel santri_kelas sudah terisi
php artisan tinker
>>> App\Models\SantriKelas::where('is_primary', true)->count()
```

### TAHAP 2: Test Aplikasi

Setelah TAHAP 1, kode sudah diupdate untuk pakai relasi baru.

Buka browser dan test:
- [ ] **Index**: Buka halaman Data Santri → Filter kelompok kelas berfungsi
- [ ] **Create**: Tambah santri baru → Pilih kelompok → Pilih kelas → Simpan
- [ ] **Edit**: Edit santri existing → Kelas otomatis terseleksi → Update
- [ ] **Show**: Detail santri → Kelompok & Kelas tampil benar
- [ ] **Delete**: Hapus santri → Tidak error
- [ ] **Foto**: Upload foto masih berfungsi normal

### TAHAP 3: Drop Kolom Lama

**SETELAH semua test di TAHAP 2 pass:**

```bash
# Backup database dulu!
mysqldump -u root sim_pkpps > backup_before_drop_kelas.sql

# Jalankan migration drop kolom
php artisan migrate

# Test lagi semua fitur
```

Jika perlu rollback:
```bash
php artisan migrate:rollback --step=1
```

---

## File yang Diubah

| File | Perubahan |
|------|-----------|
| `app/Console/Commands/MigrateSantriToNewKelas.php` | **BARU** — Command migrasi data |
| `app/Models/Santri.php` | Hapus `kelas` dari fillable, simplify accessor, tambah scope |
| `app/Http/Controllers/Admin/SantriController.php` | Semua method: pakai relasi baru + eager loading |
| `resources/views/admin/santri/form.blade.php` | Dropdown bertingkat Kelompok → Kelas (vanilla JS) |
| `resources/views/admin/santri/index.blade.php` | Filter kelompok + kelas dari relasi di tabel |
| `resources/views/admin/santri/show.blade.php` | Tampil kelompok + kelas dari relasi |
| `database/migrations/2026_02_14_..._drop_kelas.php` | **BARU** — Drop kolom `kelas` dari `santris` |

---

## Troubleshooting

### Error: "Kelas wajib dipilih" saat create/edit
- Pastikan tabel `kelompok_kelas` dan `kelas` sudah ada data
- Pastikan `is_active = true` pada kelompok & kelas

### Dropdown kelas tidak muncul saat edit
- Pastikan relasi `kelasPrimary` sudah ter-load: controller harus `$santri->load('kelasPrimary.kelas.kelompok')`

### Kolom kelas masih ada di database
- Jalankan `php artisan migrate` untuk menjalankan migration drop kolom
- Atau jalankan manual: `ALTER TABLE santris DROP COLUMN kelas;`

### Rollback penuh
```bash
# 1. Rollback drop kolom
php artisan migrate:rollback --step=1

# 2. Restore kode lama dari git
git checkout -- app/Models/Santri.php
git checkout -- app/Http/Controllers/Admin/SantriController.php
git checkout -- resources/views/admin/santri/

# 3. Bersihkan santri_kelas jika perlu
php artisan tinker
>>> App\Models\SantriKelas::truncate()
```
