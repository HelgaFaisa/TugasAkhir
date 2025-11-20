@extends('layouts.app')

@section('title', 'Tambah Berita Baru')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Berita Baru</h2>
</div>

<!-- Alert Errors -->
@if($errors->any())
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
        <ul style="margin: 10px 0 0 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-box">
    <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Judul Berita -->
        <div class="form-group">
            <label for="judul">
                <i class="fas fa-heading form-icon"></i>
                Judul Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <input type="text" 
                   id="judul" 
                   name="judul" 
                   class="form-control @error('judul') is-invalid @enderror" 
                   value="{{ old('judul') }}" 
                   placeholder="Masukkan judul berita..." 
                   required>
            @error('judul')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Konten Berita -->
        <div class="form-group">
            <label for="konten">
                <i class="fas fa-align-left form-icon"></i>
                Konten Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <textarea id="konten" 
                      name="konten" 
                      class="form-control @error('konten') is-invalid @enderror" 
                      rows="10" 
                      placeholder="Tulis konten berita di sini..." 
                      required>{{ old('konten') }}</textarea>
            @error('konten')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Penulis & Gambar -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="penulis">
                    <i class="fas fa-user-edit form-icon"></i>
                    Penulis <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="text" 
                       id="penulis" 
                       name="penulis" 
                       class="form-control @error('penulis') is-invalid @enderror" 
                       value="{{ old('penulis') }}" 
                       placeholder="Nama penulis berita" 
                       required>
                @error('penulis')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="gambar">
                    <i class="fas fa-image form-icon"></i>
                    Gambar Berita (Opsional)
                </label>
                <input type="file" 
                       id="gambar" 
                       name="gambar" 
                       class="form-control @error('gambar') is-invalid @enderror" 
                       accept="image/*">
                <small class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                @error('gambar')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Target & Status -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="target_berita">
                    <i class="fas fa-bullseye form-icon"></i>
                    Target Berita <span style="color: var(--danger-color);">*</span>
                </label>
                <select id="target_berita" 
                        name="target_berita" 
                        class="form-control @error('target_berita') is-invalid @enderror" 
                        required>
                    <option value="">-- Pilih Target --</option>
                    <option value="semua" {{ old('target_berita') == 'semua' ? 'selected' : '' }}>
                        Semua Santri
                    </option>
                    <option value="kelas_tertentu" {{ old('target_berita') == 'kelas_tertentu' ? 'selected' : '' }}>
                        Kelas Tertentu
                    </option>
                    <option value="santri_tertentu" {{ old('target_berita') == 'santri_tertentu' ? 'selected' : '' }}>
                        Santri Tertentu
                    </option>
                </select>
                @error('target_berita')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">
                    <i class="fas fa-toggle-on form-icon"></i>
                    Status Berita <span style="color: var(--danger-color);">*</span>
                </label>
                <select id="status" 
                        name="status" 
                        class="form-control @error('status') is-invalid @enderror" 
                        required>
                    <option value="">-- Pilih Status --</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                        Draft (Belum Dipublikasi)
                    </option>
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>
                        Published (Dipublikasi)
                    </option>
                </select>
                @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Section: Pilih Kelas Tertentu -->
        <div id="kelas-section" class="form-group" style="display: none;">
            <label>
                <i class="fas fa-graduation-cap form-icon"></i>
                Pilih Kelas yang Akan Menerima Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <div style="border: 2px solid var(--primary-light); border-radius: var(--border-radius-sm); padding: 20px; background-color: var(--primary-light);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
                    @foreach($kelasOptions as $kelas)
                    <div style="background: white; padding: 12px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
                        <label style="display: flex; align-items: center; margin: 0; cursor: pointer;">
                            <input type="checkbox" 
                                   id="kelas_{{ $kelas }}" 
                                   name="target_kelas[]" 
                                   value="{{ $kelas }}" 
                                   class="kelas-checkbox"
                                   style="margin-right: 10px; width: 18px; height: 18px;"
                                   {{ in_array($kelas, old('target_kelas', [])) ? 'checked' : '' }}>
                            <span style="font-weight: 600; color: var(--text-color);">
                                Kelas {{ $kelas }}
                            </span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <small class="form-text">
                <i class="fas fa-info-circle"></i>
                <span id="selected-kelas-count">0</span> kelas dipilih dari {{ count($kelasOptions) }} total kelas.
            </small>
        </div>

        <!-- Section: Pilih Santri Tertentu -->
        <div id="santri-section" class="form-group" style="display: none;">
            <label>
                <i class="fas fa-users form-icon"></i>
                Pilih Santri yang Akan Menerima Berita <span style="color: var(--danger-color);">*</span>
            </label>
            
            <!-- Select All -->
            <div style="background: var(--primary-light); padding: 12px; border-radius: var(--border-radius-sm); margin-bottom: 10px;">
                <label style="display: flex; align-items: center; margin: 0; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" 
                           id="select-all" 
                           style="margin-right: 10px; width: 20px; height: 20px;">
                    <span style="color: var(--primary-dark);">
                        <i class="fas fa-check-double"></i> Pilih Semua Santri
                    </span>
                </label>
            </div>
            
            <!-- List Santri -->
            <div style="border: 2px solid var(--primary-light); border-radius: var(--border-radius-sm); padding: 15px; max-height: 400px; overflow-y: auto; background-color: #FAFAFA;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                    @foreach($santri as $s)
                    <div style="background: white; padding: 12px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                        <label style="display: flex; align-items: center; gap: 10px; margin: 0; cursor: pointer;">
                            <input type="checkbox" 
                                id="santri_{{ $s->id_santri }}" 
                                name="santri_tertentu[]" 
                                value="{{ $s->id_santri }}" 
                                class="santri-checkbox"
                                style="width: 18px; height: 18px; flex-shrink: 0;"
                                {{ in_array($s->id_santri, old('santri_tertentu', [])) ? 'checked' : '' }}>
                            
                            <!-- Hanya tampilkan initial, tanpa foto -->
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">
                                {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                            </div>
                            
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--primary-color); font-size: 0.85em;">
                                    {{ $s->id_santri }}
                                </div>
                                <div style="font-weight: 500; color: var(--text-color); font-size: 0.9em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $s->nama_lengkap }}
                                </div>
                                <div style="font-size: 0.8em; color: var(--text-light);">
                                    {{ $s->kelas }}
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <small class="form-text">
                <i class="fas fa-info-circle"></i>
                <span id="selected-count">0</span> santri dipilih dari {{ $santri->count() }} total santri aktif.
            </small>
        </div>

        <!-- Submit Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--primary-light);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Berita
            </button>
            <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetBerita = document.getElementById('target_berita');
    const santriSection = document.getElementById('santri-section');
    const kelasSection = document.getElementById('kelas-section');
    const selectAll = document.getElementById('select-all');
    const santriCheckboxes = document.querySelectorAll('.santri-checkbox');
    const kelasCheckboxes = document.querySelectorAll('.kelas-checkbox');
    const selectedCount = document.getElementById('selected-count');
    const selectedKelasCount = document.getElementById('selected-kelas-count');

    // Toggle sections berdasarkan target berita
    targetBerita.addEventListener('change', function() {
        santriSection.style.display = 'none';
        kelasSection.style.display = 'none';
        
        if (this.value === 'santri_tertentu') {
            santriSection.style.display = 'block';
        } else if (this.value === 'kelas_tertentu') {
            kelasSection.style.display = 'block';
        } else {
            // Reset checkboxes
            if (selectAll) selectAll.checked = false;
            santriCheckboxes.forEach(cb => cb.checked = false);
            kelasCheckboxes.forEach(cb => cb.checked = false);
            updateSelectedCount();
            updateSelectedKelasCount();
        }
    });

    // Trigger on page load jika ada old value
    if (targetBerita.value === 'santri_tertentu') {
        santriSection.style.display = 'block';
    } else if (targetBerita.value === 'kelas_tertentu') {
        kelasSection.style.display = 'block';
    }

    // Select All functionality untuk santri
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            santriCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    // Update select all ketika checkbox santri individual berubah
    santriCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.santri-checkbox:checked').length;
            if (selectAll) {
                selectAll.checked = checkedCount === santriCheckboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < santriCheckboxes.length;
            }
            updateSelectedCount();
        });
    });

    // Update counter untuk kelas
    kelasCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedKelasCount);
    });

    // Functions untuk update counter
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.santri-checkbox:checked').length;
        if (selectedCount) selectedCount.textContent = checkedCount;
    }

    function updateSelectedKelasCount() {
        const checkedCount = document.querySelectorAll('.kelas-checkbox:checked').length;
        if (selectedKelasCount) selectedKelasCount.textContent = checkedCount;
    }

    // Initial count
    updateSelectedCount();
    updateSelectedKelasCount();
});
</script>
@endsection