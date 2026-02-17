# Flutter Multiple Kelas UI Documentation

## Overview
Aplikasi mobile Flutter telah diupdate untuk menampilkan **multiple kelas** per santri dengan UI yang clean, informatif, dan responsive.

---

## UI/UX Changes Summary

### BEFORE (Version 1.0)
```
┌────────────────────────────────────┐
│  Profil Santri                     │
├────────────────────────────────────┤
│      [Foto Avatar]                 │
│      Ahmad Santoso                 │
│      S001                          │
│      [Aktif]                       │
├────────────────────────────────────┤
│ 📋 Informasi Dasar                 │
│  ID Santri:      S001              │
│  NIS:            2024001           │
│  Nama Lengkap:   Ahmad Santoso     │
│  Jenis Kelamin:  Laki-laki         │
│  Kelas:          Lambatan B  ← SINGLE KELAS
│  Status:         Aktif             │
└────────────────────────────────────┘
```

### AFTER (Version 2.0)
```
┌────────────────────────────────────┐
│  Profil Santri                     │
├────────────────────────────────────┤
│      [Foto Avatar]                 │
│      Ahmad Santoso                 │
│      S001                          │
│   ┌──────────────────────┐         │
│   │ 📚 Lambatan B        │ ← Primary badge
│   └──────────────────────┘         │
│   +3 kelas lainnya ↓     ← Hint    │
│      [Aktif]                       │
├────────────────────────────────────┤
│ 📋 Informasi Dasar                 │
│  ID Santri:      S001              │
│  NIS:            2024001           │
│  Nama Lengkap:   Ahmad Santoso     │
│  Jenis Kelamin:  Laki-laki         │
│  Status:         Aktif             │ ← Kelas dihapus
├────────────────────────────────────┤
│ 🎓 Kelas yang Diikuti   ← NEW SECTION
│                                    │
│ ▼ 🔵 PB (1 kelas)       ← Expanded │
│   ├─ PB Putra A                    │
│   │  KLS001                        │
│                                    │
│ ▼ 🟠 Lambatan (2 kelas) ← Expanded │
│   ├─ Lambatan B [⭐ Utama]  ← Primary
│   │  KLS005                        │
│   ├─ Lambatan A                    │
│   │  KLS006                        │
│                                    │
│ ▶ 🟢 Cepatan (1 kelas)  ← Collapsed│
│                                    │
│ ▶ 🔴 Hadist (1 kelas)   ← Collapsed│
└────────────────────────────────────┘
```

---

## New Features

### 1. Primary Kelas Badge (Header)
**Location:** Di header, antara ID Santri dan Status Badge

**Features:**
- Menampilkan kelas utama (primary kelas)
- Icon 📚 (sekolah)
- Background: Semi-transparent white
- Border: White border (subtle)
- Hint: "+X kelas lainnya" jika total kelas > 1

**Code:**
```dart
Widget _buildPrimaryKelasBadge() { ... }
```

### 2. Kelas yang Diikuti Section
**Location:** Setelah "Informasi Dasar", sebelum "Alamat & Asal"

**Features:**
- Section card dengan icon 🎓
- ExpansionTile per kelompok (collapsible)
- Color-coded badges per kelompok
- Sortir: Primary kelas di atas
- Badge "⭐ Utama" untuk primary kelas

**Code:**
```dart
Widget _buildKelasListSection() { ... }
Widget _buildKelompokExpansionTile(String kelompokName, List kelasItems) { ... }
```

### 3. Color Coding System
Setiap kelompok memiliki warna unik:

| Kelompok | Color | Hex Code | Icon |
|----------|-------|----------|------|
| PB / Pondok | 🔵 Blue | #3b82f6 | 🏫 Icons.school |
| Lambatan | 🟠 Orange | #fb923c | 📖 Icons.menu_book |
| Cepatan | 🟢 Green | #10b981 | ⚡ Icons.speed |
| Tahfidz | 🟣 Purple | #7C3AED | 📚 Icons.auto_stories |
| Hadist | 🔵 Teal | #14b8a6 | 📗 Icons.import_contacts |
| Default | ⚫ Gray | #6b7280 | 🎓 Icons.class_ |

**Code:**
```dart
Color _getKelompokColor(String kelompokName) { ... }
IconData _getKelompokIcon(String kelompokName) { ... }
```

---

## UI Component Breakdown

### ExpansionTile Structure

```dart
Container (Border + Border Radius)
└─ Theme (Hide default divider)
   └─ ExpansionTile
      ├─ Leading: Colored icon badge
      ├─ Title: Kelompok name (bold, colored)
      ├─ Subtitle: "X kelas" (gray)
      └─ Children: List of kelas items
         └─ Container (Kelas item)
            ├─ Left: Nama kelas + Kode kelas
            └─ Right: Badge "⭐ Utama" (if primary)
```

### Primary Badge Indicator

**Styling:**
- background: Gold (#fbbf24)
- Icon: ⭐ Star (white, size 12)
- Text: "Utama" (white, size 10, bold)
- Padding: 8px horizontal, 4px vertical
- Border radius: 8px

### Kelas Item Styling

**Primary Kelas:**
- Background: Kelompok color with 10% opacity
- Border: Kelompok color with 30% opacity, width 1.5px
- Text: Bold, kelompok color
- Badge: "⭐ Utama" visible

**Non-Primary Kelas:**
- Background: Light gray (5% opacity)
- Border: None
- Text: Semi-bold, black87
- Badge: Hidden

---

## Responsive Design

### Screen Sizes Supported
- Min width: 320px (iPhone SE)
- Max width: 800px (iPad)
- Optimal: 360-428px (Most smartphones)

### Adaptive Behavior
- ExpansionTile: Auto-adjust height
- Text overflow: Ellipsis
- Padding: Proportional to screen width
- Card elevation: 2 (consistent)

---

## Performance Optimizations

### 1. Lazy Loading
- Section "Kelas yang Diikuti" hanya render saat visible
- ExpansionTile default collapsed
- Children di-render saat expanded

### 2. Minimal Dependencies
- **NO EXTERNAL PACKAGES** untuk kelas display
- Hanya Flutter built-in widgets:
  - ExpansionTile
  - Card
  - Container
  - Row, Column
  - Icon, Text

### 3. No Heavy Assets
- Semua icon menggunakan `Icons.*` (Flutter built-in)
- No image assets loaded
- No SVG files

### 4. Efficient State Management
- Single `_santriData` map
- No redundant API calls
- Cache-first strategy dengan SharedPreferences

---

## Code Files Modified

### File: `lib/features/profil/profil_page.dart`

**New Methods Added:**
1. `_buildPrimaryKelasBadge()` - Lines ~305-360
2. `_buildKelasListSection()` - Lines ~365-440
3. `_buildKelompokExpansionTile()` - Lines ~445-570
4. `_getKelompokColor()` - Lines ~575-595
5. `_getKelompokIcon()` - Lines ~600-620

**Modified Sections:**
1. `build()` method - Added conditional section display
2. `_buildHeader()` - Added primary kelas badge call
3. "Informasi Dasar" card - Removed kelas row

**Total Lines:** ~620 lines (dari ~300 lines sebelumnya)

---

## Error Handling

### Defensive Programming

```dart
// Handle null kelas_list
if (_santriData?['kelas_list'] != null && 
    (_santriData!['kelas_list'] as List).isNotEmpty) {
  _buildKelasListSection()
}

// Handle null kelompok
final kelompokName = kelompok['kelompok_name'] ?? 'Unknown';
final kelasItems = kelompok['kelas'] as List? ?? [];

// Handle null kelas properties
final namaKelas = kelas['nama_kelas'] ?? '-';
final kodeKelas = kelas['kode_kelas'] ?? '-';
final isPrimary = kelas['is_primary'] == true;
```

### Empty State

```dart
if (kelasList.isEmpty) {
  return _buildSectionCard(
    title: 'Kelas yang Diikuti',
    icon: Icons.class_,
    children: [
      Center(
        child: Text(
          'Belum mengikuti kelas apapun',
          style: TextStyle(color: Colors.grey[600]),
        ),
      ),
    ],
  );
}
```

---

## Testing Guide

### Manual Testing Steps

#### Test 1: Display Multiple Kelas
1. Login sebagai santri yang punya multiple kelas
2. Navigasi ke tab "Profil"
3. **Expected:**
   - Header menampilkan primary kelas badge
   - Hint "+X kelas lainnya" muncul
   - Section "Kelas yang Diikuti" visible
   - Kelompok di-group dengan benar
   - Primary kelas punya badge "⭐ Utama"

#### Test 2: Expansion/Collapse
1. Tap kelompok yang collapsed
2. **Expected:** ExpansionTile expand, menampilkan kelas items
3. Tap lagi
4. **Expected:** ExpansionTile collapse

#### Test 3: Primary Badge Visibility
1. Cari kelas dengan `is_primary = true`
2. **Expected:** Badge "⭐ Utama" muncul di kanan kelas item
3. Cari kelas dengan `is_primary = false`
4. **Expected:** Badge tidak muncul

#### Test 4: Empty State
1. Login sebagai santri belum punya kelas
2. **Expected:** 
   - Section "Kelas yang Diikuti" TIDAK muncul
   - Field kelas di "Informasi Dasar" tidak ada

#### Test 5: Single Kelas
1. Login sebagai santri dengan 1 kelas saja
2. **Expected:**
   - Primary kelas badge muncul
   - Hint "+X kelas lainnya" TIDAK muncul (karena cuma 1)
   - Section "Kelas yang Diikuti" muncul dengan 1 kelompok

#### Test 6: Color Coding
1. Cek kelompok "PB" → Blue
2. Cek kelompok "Lambatan" → Orange
3. Cek kelompok "Cepatan" → Green
4. Cek kelompok "Tahfidz" → Purple
5. Cek kelompok "Hadist" → Teal

#### Test 7: Responsive
1. Test di screen 320px (iPhone SE)
2. Test di screen 375px (iPhone 13)
3. Test di screen 428px (iPhone 13 Pro Max)
4. **Expected:** No horizontal overflow, text ellipsis bekerja

#### Test 8: Pull-to-Refresh
1. Swipe down di profil page
2. **Expected:** Loading indicator muncul, data refresh dari API

---

## Debugging Tips

### Problem: Section tidak muncul
**Check:**
```dart
print('kelas_list: ${_santriData?['kelas_list']}');
print('is List: ${_santriData?['kelas_list'] is List}');
print('isEmpty: ${(_santriData?['kelas_list'] as List?)?.isEmpty}');
```

### Problem: ExpansionTile tidak expand
**Check:**
- Pastikan `Theme` wrapper ada (untuk hide default divider)
- Cek console error saat tap

### Problem: Badge "Utama" tidak muncul
**Check:**
```dart
print('isPrimary: ${kelas['is_primary']}');
print('isPrimary type: ${kelas['is_primary'].runtimeType}');
```

### Problem: Color salah
**Check:**
```dart
print('kelompokName: $kelompokName');
print('color: ${_getKelompokColor(kelompokName)}');
```

---

## Future Enhancements (Optional)

### Phase 2 (Nice to Have)

1. **Smooth Animation**
   - Add `AnimatedSwitcher` untuk smooth transition
   - Fade animation saat expand/collapse

2. **Search/Filter**
   - Search box untuk cari kelas
   - Filter by kelompok

3. **Tap to Detail**
   - Tap kelas item → Navigate ke detail kelas page
   - Show jadwal, materi, guru, dll

4. **Statistics**
   - Show kehadiran per kelas
   - Show nilai rata-rata per kelas

### Phase 3 (Advanced)

1. **Tahun Ajaran**
   - Display tahun ajaran per kelas
   - Filter by tahun ajaran

2. **Kelas History**
   - Show riwayat kelas tahun-tahun sebelumnya

3. **QR Code**
   - Generate QR code untuk absensi per kelas

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-02-14 | Initial: Single kelas display |
| 2.0.0 | 2026-02-14 | **NEW**: Multiple kelas with ExpansionTile, color coding, primary badge |

---

## Troubleshooting

### Flutter Analyze Errors
```bash
cd sim_mobile
flutter analyze
```

### Flutter Format
```bash
flutter format lib/features/profil/profil_page.dart
```

### Build APK (Test)
```bash
flutter build apk --debug
```

---

## Contact & Support

- File: `lib/features/profil/profil_page.dart`
- Backup: `lib/features/profil/profil_page.dart.backup`
- Flutter version: 3.x
- Dart version: 3.x
