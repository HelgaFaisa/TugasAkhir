# REFACTORING QUICK REFERENCE
# ============================

## 1. MIGRATE DATA
```bash
# Test migration (dry-run)
php artisan migrate:santri-kelas --dry-run

# Run actual migration
php artisan migrate:santri-kelas

# Force overwrite existing data
php artisan migrate:santri-kelas --force
```

## 2. SCAN CODEBASE
```bash
# Generate usage report
php scan_kelas_usage.php

# View report
cat KELAS_USAGE_MAP.md
# or open in editor
code KELAS_USAGE_MAP.md
```

## 3. REFACTORING PATTERNS

### Pattern 1: Display in Views (MEDIUM Priority)
```blade
<!-- OLD -->
{{ $santri->kelas }}

<!-- NEW (Backward Compatible) -->
{{ $santri->kelas_name }}
```

### Pattern 2: Filter in Controllers (HIGH Priority)
```php
// OLD
$santris = Santri::where('kelas', 'PB')->get();

// NEW
$santris = Santri::whereHas('kelasSantri', function($q) {
    $q->where('id_kelas', 1); // PB = 1
})->get();

// OR using kelas relation
$kelas = Kelas::where('nama_kelas', 'PB')->first();
$santris = $kelas->santris;
```

### Pattern 3: Multiple Kelas Filter (HIGH Priority)
```php
// OLD
$santris = Santri::whereIn('kelas', ['PB', 'Lambatan'])->get();

// NEW
$kelasIds = Kelas::whereIn('nama_kelas', ['PB', 'Lambatan'])->pluck('id');
$santris = Santri::whereHas('kelasSantri', function($q) use ($kelasIds) {
    $q->whereIn('id_kelas', $kelasIds);
})->get();
```

### Pattern 4: Kegiatan Eligible Santris (HIGH Priority)
```php
// OLD: Manual filter by kelas
$santris = Santri::whereIn('kelas', ['PB', 'Lambatan'])->get();

// NEW: Use helper method
$santris = $kegiatan->getEligibleSantris();
// This automatically handles:
// - Umum (all santri)
// - Specific kelas (filtered)
```

### Pattern 5: Check Santri Kelas
```php
// NEW: Check if santri in specific kelas
if ($santri->hasKelas($id_kelas)) {
    // Do something
}

// Get all kelas for santri in current year
$kelasList = $santri->getKelasByTahun('2024/2025');
```

## 4. TESTING CHECKLIST

```bash
# After each refactor, test:
□ Display: Santri detail page shows correct kelas
□ Filter: Santri list filter by kelas works
□ Stats: Dashboard statistics by kelas accurate
□ Kegiatan: Filtering by kelas works
□ Absensi: Shows correct santri per kegiatan
□ Reports: Include correct kelas information
□ Mobile: API returns kelas data correctly
```

## 5. KELAS ID MAPPING

```
PB        -> ID: 1  (KLS001)
Lambatan  -> ID: 2  (KLS002)
Cepatan   -> ID: 3  (KLS003)
```

## 6. COMMON ISSUES & FIXES

### Issue: Query too slow
```php
// Add eager loading
$santris = Santri::with(['kelasPrimary.kelas'])->get();
```

### Issue: Kelas not showing
```php
// Make sure santri has been migrated
php artisan migrate:santri-kelas

// Check in database
SELECT * FROM santri_kelas WHERE id_santri = 'S001';
```

### Issue: Multiple kelas showing
```php
// Get only primary kelas
$primaryKelas = $santri->kelasPrimary->kelas->nama_kelas;
```

## 7. ROLLBACK (If needed)

```php
// If something goes wrong, you can:
// 1. Delete migrated data
DELETE FROM santri_kelas WHERE tahun_ajaran = '2024/2025';

// 2. Re-run migration
php artisan migrate:santri-kelas --force

// 3. Old column 'kelas' is still there for fallback
```

## 8. FILES TO PRIORITIZE

### HIGH (Do First):
1. app/Http/Controllers/Admin/CapaianController.php
2. app/Http/Controllers/Admin/SantriController.php
3. Any controller with where('kelas') or whereIn('kelas')

### MEDIUM (Do After High):
1. All blade view files (24 files)
2. Change {{ $santri->kelas }} to {{ $santri->kelas_name }}

### LOW (Do Last):
1. API controllers (already may work with accessor)
2. Other controllers without direct kelas query

## 9. USEFUL COMMANDS

```bash
# Check migration status
php artisan migrate:status

# Rollback last migration (if needed)
php artisan migrate:rollback

# Seed kelas data
php artisan db:seed --class=KelompokKelasSeeder
php artisan db:seed --class=KelasSeeder

# Check current data
php artisan tinker
>>> Santri::with('kelasPrimary.kelas')->first()->kelas_name
>>> SantriKelas::count()
```
