# Santri.kelas Usage Mapping

_Generated: 2026-02-12 16:30:36_

This document maps all usage of `$santri->kelas` and related patterns in the codebase to guide refactoring to the new kelas system.

---

## 📊 Summary

- **Total files with kelas usage:** 40
- **Total matches found:** 115

---

## 🎯 Priority Levels

### 🔴 HIGH Priority (Break functionality)

- **app/Http/Controllers/Admin/CapaianController.php**
  - Issue: Query filtering by kelas column
  - Action Required: Update to use kelasSantri relationship

- **app/Http/Controllers/Admin/SantriController.php**
  - Issue: Query filtering by kelas column
  - Action Required: Update to use kelasSantri relationship

- **database/migrations/2025_09_29_033444_create_santris_table.php**
  - Issue: Database schema definition
  - Action Required: Review but DO NOT modify old migrations

- **database/migrations/2025_10_31_064743_create_materi_table.php**
  - Issue: Database schema definition
  - Action Required: Review but DO NOT modify old migrations

### 🟡 MEDIUM Priority (UI/Display)

- **app/Models/Materi.php**
  - Issue: Model attribute or accessor
  - Action Required: Review accessor implementation

- **app/Models/Santri.php**
  - Issue: Model attribute or accessor
  - Action Required: Review accessor implementation

- **resources/views/admin/berita/show.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/capaian/create.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/capaian/export-rapor.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/capaian/index.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/capaian/riwayat-santri.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kegiatan/absensi/input.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kegiatan/kartu/cetak.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kegiatan/kartu/daftar.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kegiatan/kartu/index.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kegiatan/riwayat/detail-santri.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kepulangan/create.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kepulangan/over-limit.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kepulangan/surat-pdf.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/kesehatan-santri/riwayat.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/pembayaran-spp/create.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/pembayaran-spp/edit.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/riwayat_pelanggaran/riwayat_santri.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/santri/form.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/santri/index.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/santri/show.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/admin/users/wali_accounts.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/santri/berita/index.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/santri/capaian/index.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

- **resources/views/santri/kegiatan/index.blade.php**
  - Issue: Display kelas in UI
  - Action Required: Change to use $santri->kelas_name accessor

### 🟢 LOW Priority (Backward compatible)

- **app/Http/Controllers/Admin/AbsensiKegiatanController.php**
  - Note: Other usage

- **app/Http/Controllers/Admin/BeritaController.php**
  - Note: Other usage

- **app/Http/Controllers/Admin/MateriController.php**
  - Note: Other usage

- **app/Http/Controllers/Admin/PembayaranSppController.php**
  - Note: Other usage

- **app/Http/Controllers/Api/ApiAuthController.php**
  - Note: Other usage

- **app/Http/Controllers/Api/ApiBeritaController.php**
  - Note: Other usage

- **app/Http/Controllers/Api/ApiCapaianController.php**
  - Note: Other usage

- **app/Http/Controllers/DashboardController.php**
  - Note: Other usage

- **app/Http/Controllers/Santri/SantriBeritaController.php**
  - Note: Other usage

- **database/seeders/KelasSeeder.php**
  - Note: Other usage

---

## 📂 Detailed Listing by Directory

### App / Http / Controllers

#### 📄 `app/Http/Controllers/Admin/AbsensiKegiatanController.php`

**Pattern: `property_access`**

- **Line 179:** `'kelas' => $santri->kelas,`

**Pattern: `kelas_column`**

- **Line 179:** `'kelas' => $santri->kelas,`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/Admin/BeritaController.php`

**Pattern: `enum_values`**

- **Line 51:** `$kelasOptions = ['PB', 'Lambatan', 'Cepatan'];`
- **Line 127:** `$kelasOptions = ['PB', 'Lambatan', 'Cepatan'];`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/Admin/CapaianController.php`

**Pattern: `where_kelas`**

- **Line 35:** `$query->where('kelas', $selectedKelas);`
- **Line 344:** `->when($kelas, fn($q) => $q->where('kelas', $kelas))`
- **Line 347:** `->when($kelas, fn($q) => $q->where('kelas', $kelas))`
- **Line 352:** `->when($kelas, fn($q) => $q->whereHas('santri', fn($sq) => $sq->where('kelas', $kelas)))`
- **Line 393:** `$kelasMateris = $materis->where('kelas', $k);`
- **Line 463:** `$filteredMateris = $kelas ? $materis->where('kelas', $kelas) : $materis;`
- **Line 480:** `$heatmapMateris = $kelas ? $materis->where('kelas', $kelas)->values() : $materis->take(15)->values();`
- **Line 835:** `$q->where('kelas', $kelas);`
- **Line 896:** `$query->where('kelas', $kelas);`

**Pattern: `property_access`**

- **Line 116:** `$materis = Materi::where('kelas', $santri->kelas)`
- **Line 123:** `'kelas' => $santri->kelas,`
- **Line 453:** `'kelas' => $santri->kelas,`
- **Line 484:** `$row = ['nama' => $santri->nama_lengkap, 'id_santri' => $santri->id_santri, 'kelas' => $santri->kelas];`

**Pattern: `kelas_column`**

- **Line 123:** `'kelas' => $santri->kelas,`
- **Line 453:** `'kelas' => $santri->kelas,`
- **Line 484:** `$row = ['nama' => $santri->nama_lengkap, 'id_santri' => $santri->id_santri, 'kelas' => $santri->kelas];`

**Pattern: `enum_values`**

- **Line 341:** `$kelasList = ['Lambatan', 'Cepatan', 'PB'];`
- **Line 708:** `$kelas = $request->input('kelas', 'Lambatan');`

**💡 Suggested Action:**
1. Replace `where('kelas')` with `whereHas('kelasSantri')`
2. Update query to use kelas ID instead of name
3. Test filter functionality thoroughly


---

#### 📄 `app/Http/Controllers/Admin/MateriController.php`

**Pattern: `kelas_column`**

- **Line 82:** `'kelas' => 'required|in:Lambatan,Cepatan,PB',`
- **Line 156:** `'kelas' => 'required|in:Lambatan,Cepatan,PB',`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/Admin/PembayaranSppController.php`

**Pattern: `property_access`**

- **Line 54:** `'kelas' => $santri->kelas,`

**Pattern: `kelas_column`**

- **Line 54:** `'kelas' => $santri->kelas,`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/Admin/SantriController.php`

**Pattern: `where_kelas`**

- **Line 38:** `$query->where('kelas', $request->kelas);`

**Pattern: `kelas_column`**

- **Line 86:** `'kelas' => 'required|in:PB,Lambatan,Cepatan',`
- **Line 154:** `'kelas' => 'required|in:PB,Lambatan,Cepatan',`

**💡 Suggested Action:**
1. Replace `where('kelas')` with `whereHas('kelasSantri')`
2. Update query to use kelas ID instead of name
3. Test filter functionality thoroughly


---

#### 📄 `app/Http/Controllers/Api/ApiAuthController.php`

**Pattern: `property_access`**

- **Line 158:** `'kelas' => $santri->kelas,`

**Pattern: `kelas_column`**

- **Line 158:** `'kelas' => $santri->kelas,`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/Api/ApiBeritaController.php`

**Pattern: `property_access`**

- **Line 42:** `->whereJsonContains('target_kelas', $santri->kelas);`
- **Line 146:** `$bolehAkses = in_array($santri->kelas, $berita->target_kelas ?? []);`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/Api/ApiCapaianController.php`

**Pattern: `property_access`**

- **Line 125:** `'kelas' => $santri->kelas,`
- **Line 490:** `->where('santris.kelas', $santri->kelas)`
- **Line 523:** `->where('santris.kelas', $santri->kelas)`
- **Line 591:** `'kelas' => $santri->kelas,`

**Pattern: `kelas_column`**

- **Line 125:** `'kelas' => $santri->kelas,`
- **Line 295:** `'kelas' => $capaian->materi->kelas,`
- **Line 591:** `'kelas' => $santri->kelas,`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/DashboardController.php`

**Pattern: `property_access`**

- **Line 204:** `'kelas' => $santri->kelas,`
- **Line 251:** `->whereJsonContains('target_kelas', $santri->kelas);`

**Pattern: `kelas_column`**

- **Line 204:** `'kelas' => $santri->kelas,`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `app/Http/Controllers/Santri/SantriBeritaController.php`

**Pattern: `property_access`**

- **Line 44:** `->whereJsonContains('target_kelas', $santri->kelas);`
- **Line 89:** `->whereJsonContains('target_kelas', $santri->kelas);`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

### App / Models

#### 📄 `app/Models/Materi.php`

**Pattern: `where_kelas`**

- **Line 80:** `return $query->where('kelas', $kelas);`

**💡 Suggested Action:**
1. Review model methods and accessors
2. Ensure backward compatibility
3. Add tests for new relations


---

#### 📄 `app/Models/Santri.php`

**Pattern: `enum_values`**

- **Line 177:** `'Lambatan' => 'Lambatan',`
- **Line 178:** `'Cepatan' => 'Cepatan',`

**Pattern: `where_kelas`**

- **Line 306:** `return $query->where('kelas', $kelas);`

**💡 Suggested Action:**
1. Review model methods and accessors
2. Ensure backward compatibility
3. Add tests for new relations


---

### Resources / views

#### 📄 `resources/views/admin/berita/show.blade.php`

**Pattern: `property_access`**

- **Line 130:** `<i class="fas fa-graduation-cap"></i> {{ $santri->kelas }}`

**Pattern: `blade_kelas`**

- **Line 130:** `<i class="fas fa-graduation-cap"></i> {{ $santri->kelas }}`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/capaian/create.blade.php`

**Pattern: `property_access`**

- **Line 25:** `data-kelas="{{ $santri->kelas }}"`
- **Line 27:** `{{ $santri->nama_lengkap }} ({{ $santri->nis }}) - Kelas: {{ $santri->kelas }}`

**Pattern: `blade_kelas`**

- **Line 25:** `data-kelas="{{ $santri->kelas }}"`
- **Line 27:** `{{ $santri->nama_lengkap }} ({{ $santri->nis }}) - Kelas: {{ $santri->kelas }}`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/capaian/export-rapor.blade.php`

**Pattern: `property_access`**

- **Line 96:** `<div class="info-item"><span class="label">Kelas</span> <span class="value">{{ $santri->kelas }}</span></div>`

**Pattern: `blade_kelas`**

- **Line 96:** `<div class="info-item"><span class="label">Kelas</span> <span class="value">{{ $santri->kelas }}</span></div>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/capaian/index.blade.php`

**Pattern: `enum_values`**

- **Line 38:** `<a href="{{ route('admin.capaian.index', array_merge(request()->except('kelas'), ['kelas' => 'PB'])) }}"`
- **Line 43:** `<a href="{{ route('admin.capaian.index', array_merge(request()->except('kelas'), ['kelas' => 'Lambatan'])) }}"`
- **Line 48:** `<a href="{{ route('admin.capaian.index', array_merge(request()->except('kelas'), ['kelas' => 'Cepatan'])) }}"`
- **Line 112:** `@if($data['santri']->kelas == 'PB')`
- **Line 114:** `@elseif($data['santri']->kelas == 'Lambatan')`

**Pattern: `kelas_column`**

- **Line 38:** `<a href="{{ route('admin.capaian.index', array_merge(request()->except('kelas'), ['kelas' => 'PB'])) }}"`
- **Line 43:** `<a href="{{ route('admin.capaian.index', array_merge(request()->except('kelas'), ['kelas' => 'Lambatan'])) }}"`
- **Line 48:** `<a href="{{ route('admin.capaian.index', array_merge(request()->except('kelas'), ['kelas' => 'Cepatan'])) }}"`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `resources/views/admin/capaian/riwayat-santri.blade.php`

**Pattern: `property_access`**

- **Line 18:** `<strong>Kelas:</strong> <span class="badge badge-secondary">{{ $santri->kelas }}</span>`

**Pattern: `blade_kelas`**

- **Line 18:** `<strong>Kelas:</strong> <span class="badge badge-secondary">{{ $santri->kelas }}</span>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kegiatan/absensi/input.blade.php`

**Pattern: `property_access`**

- **Line 63:** `<td><span class="badge badge-secondary">{{ $santri->kelas }}</span></td>`

**Pattern: `blade_kelas`**

- **Line 63:** `<td><span class="badge badge-secondary">{{ $santri->kelas }}</span></td>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kegiatan/kartu/cetak.blade.php`

**Pattern: `property_access`**

- **Line 423:** `<span class="value">: @if(isset($santri)){{ $santri->kelas }}@else Lambatan @endif</span>`

**Pattern: `blade_kelas`**

- **Line 423:** `<span class="value">: @if(isset($santri)){{ $santri->kelas }}@else Lambatan @endif</span>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kegiatan/kartu/daftar.blade.php`

**Pattern: `property_access`**

- **Line 29:** `<td><span class="badge badge-secondary">{{ $santri->kelas }}</span></td>`

**Pattern: `blade_kelas`**

- **Line 29:** `<td><span class="badge badge-secondary">{{ $santri->kelas }}</span></td>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kegiatan/kartu/index.blade.php`

**Pattern: `property_access`**

- **Line 60:** `<td><span class="badge badge-secondary">{{ $santri->kelas }}</span></td>`

**Pattern: `blade_kelas`**

- **Line 60:** `<td><span class="badge badge-secondary">{{ $santri->kelas }}</span></td>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kegiatan/riwayat/detail-santri.blade.php`

**Pattern: `property_access`**

- **Line 15:** `Kelas: <strong>{{ $santri->kelas }}</strong> |`

**Pattern: `blade_kelas`**

- **Line 15:** `Kelas: <strong>{{ $santri->kelas }}</strong> |`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kepulangan/create.blade.php`

**Pattern: `property_access`**

- **Line 49:** `{{ $santri->nama_lengkap }} ({{ $santri->id_santri }} - {{ $santri->kelas }})`

**Pattern: `blade_kelas`**

- **Line 49:** `{{ $santri->nama_lengkap }} ({{ $santri->id_santri }} - {{ $santri->kelas }})`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kepulangan/over-limit.blade.php`

**Pattern: `property_access`**

- **Line 78:** `<td>{{ $santri->kelas }}</td>`

**Pattern: `blade_kelas`**

- **Line 78:** `<td>{{ $santri->kelas }}</td>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kepulangan/surat-pdf.blade.php`

**Pattern: `property_access`**

- **Line 269:** `<div class="data-value">{{ $santri->kelas }}</div>`
- **Line 394:** `<td style="padding: 5px;">: {{ $santri->kelas }}</td>`

**Pattern: `blade_kelas`**

- **Line 269:** `<div class="data-value">{{ $santri->kelas }}</div>`
- **Line 394:** `<td style="padding: 5px;">: {{ $santri->kelas }}</td>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/kesehatan-santri/riwayat.blade.php`

**Pattern: `property_access`**

- **Line 21:** `<strong>Kelas:</strong> {{ $santri->kelas }}<br>`

**Pattern: `blade_kelas`**

- **Line 21:** `<strong>Kelas:</strong> {{ $santri->kelas }}<br>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/pembayaran-spp/create.blade.php`

**Pattern: `property_access`**

- **Line 35:** `{{ $santri->id_santri }} - {{ $santri->nama_lengkap }} ({{ $santri->kelas }})`

**Pattern: `blade_kelas`**

- **Line 35:** `{{ $santri->id_santri }} - {{ $santri->nama_lengkap }} ({{ $santri->kelas }})`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/pembayaran-spp/edit.blade.php`

**Pattern: `property_access`**

- **Line 36:** `{{ $santri->id_santri }} - {{ $santri->nama_lengkap }} ({{ $santri->kelas }})`

**Pattern: `blade_kelas`**

- **Line 36:** `{{ $santri->id_santri }} - {{ $santri->nama_lengkap }} ({{ $santri->kelas }})`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/riwayat_pelanggaran/riwayat_santri.blade.php`

**Pattern: `property_access`**

- **Line 33:** `{{ $santri->id_santri }} | {{ $santri->kelas }}`

**Pattern: `blade_kelas`**

- **Line 33:** `{{ $santri->id_santri }} | {{ $santri->kelas }}`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/santri/form.blade.php`

**Pattern: `property_access`**

- **Line 87:** `<option value="PB" {{ old('kelas', $isEdit ? $santri->kelas : '') == 'PB' ? 'selected' : '' }}>PB (Pembinaan)</option>`
- **Line 88:** `<option value="Lambatan" {{ old('kelas', $isEdit ? $santri->kelas : '') == 'Lambatan' ? 'selected' : '' }}>Lambatan</option>`
- **Line 89:** `<option value="Cepatan" {{ old('kelas', $isEdit ? $santri->kelas : '') == 'Cepatan' ? 'selected' : '' }}>Cepatan</option>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/santri/index.blade.php`

**Pattern: `property_access`**

- **Line 89:** `<td><strong>{{ $santri->kelas }}</strong></td>`

**Pattern: `blade_kelas`**

- **Line 89:** `<td><strong>{{ $santri->kelas }}</strong></td>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/santri/show.blade.php`

**Pattern: `property_access`**

- **Line 75:** `<strong style="color: #6FBA9D; font-size: 1.1rem;">{{ $santri->kelas }}</strong>`
- **Line 76:** `@if($santri->kelas == 'PB')`

**Pattern: `blade_kelas`**

- **Line 75:** `<strong style="color: #6FBA9D; font-size: 1.1rem;">{{ $santri->kelas }}</strong>`

**Pattern: `enum_values`**

- **Line 76:** `@if($santri->kelas == 'PB')`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/admin/users/wali_accounts.blade.php`

**Pattern: `property_access`**

- **Line 95:** `<td>{{ $santri->kelas }}</td>`

**Pattern: `blade_kelas`**

- **Line 95:** `<td>{{ $santri->kelas }}</td>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/santri/berita/index.blade.php`

**Pattern: `property_access`**

- **Line 10:** `Informasi terbaru untuk <strong>{{ $santri->kelas }}</strong>`

**Pattern: `blade_kelas`**

- **Line 10:** `Informasi terbaru untuk <strong>{{ $santri->kelas }}</strong>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/santri/capaian/index.blade.php`

**Pattern: `property_access`**

- **Line 56:** `<div class="card-value-small">{{ $santri->kelas }}</div>`

**Pattern: `blade_kelas`**

- **Line 56:** `<div class="card-value-small">{{ $santri->kelas }}</div>`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

#### 📄 `resources/views/santri/kegiatan/index.blade.php`

**Pattern: `property_access`**

- **Line 9:** `{{ $santri->nama_lengkap }} - Kelas {{ $santri->kelas }}`

**Pattern: `blade_kelas`**

- **Line 9:** `{{ $santri->nama_lengkap }} - Kelas {{ $santri->kelas }}`

**💡 Suggested Action:**
1. Replace `{{ $santri->kelas }}` with `{{ $santri->kelas_name }}`
2. Test display in browser


---

### Database / migrations

#### 📄 `database/migrations/2025_09_29_033444_create_santris_table.php`

**Pattern: `enum_values`**

- **Line 25:** `$table->enum('kelas', ['PB', 'Lambatan', 'Cepatan']); // PB = Pembinaan`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

#### 📄 `database/migrations/2025_10_31_064743_create_materi_table.php`

**Pattern: `enum_values`**

- **Line 18:** `$table->enum('kelas', ['Lambatan', 'Cepatan', 'PB'])->index();`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

### Database / seeders

#### 📄 `database/seeders/KelasSeeder.php`

**Pattern: `enum_values`**

- **Line 29:** `'nama_kelas' => 'PB',`
- **Line 38:** `'nama_kelas' => 'Lambatan',`
- **Line 47:** `'nama_kelas' => 'Cepatan',`

**💡 Suggested Action:**
Review usage and update as needed based on context.

---

## 📖 Refactoring Guide

### General Patterns

#### 1. Display in Views (Blade)
```php
// OLD:
{{ $santri->kelas }}

// NEW (backward compatible):
{{ $santri->kelas_name }}
```

#### 2. Filter in Controllers
```php
// OLD:
$santris = Santri::where('kelas', 'PB')->get();

// NEW:
$santris = Santri::whereHas('kelasSantri', function($q) {
    $q->where('id_kelas', 1); // PB = 1
})->get();
```

#### 3. Kegiatan-Kelas Relation
```php
// OLD: Filter santri by kelas for kegiatan
$santris = Santri::whereIn('kelas', ['PB', 'Lambatan'])->get();

// NEW: Use kegiatan relation
$santris = $kegiatan->getEligibleSantris();
```

### Testing Checklist

- [ ] Santri detail page displays correct kelas
- [ ] Santri list filter by kelas works
- [ ] Dashboard statistics by kelas accurate
- [ ] Kegiatan filtering by kelas works
- [ ] Absensi shows correct santri per kegiatan
- [ ] Reports include correct kelas information
- [ ] Mobile API returns kelas data correctly

