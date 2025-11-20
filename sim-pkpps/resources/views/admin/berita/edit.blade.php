@extends('layouts.app')

@section('title', 'Edit Berita')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Berita</h2>
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
    <form action="{{ route('admin.berita.update', $berita->id_berita) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- ID Berita (Read-only) -->
        <div style="background: var(--primary-light); padding: 15px; border-radius: var(--border-radius-sm); margin-bottom: 20px;">
            <strong style="color: var(--primary-dark);">
                <i class="fas fa-id-card"></i> ID Berita: {{ $berita->id_berita }}
            </strong>
        </div>
        
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
                   value="{{ old('judul', $berita->judul) }}" 
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
                      required>{{ old('konten', $berita->konten) }}</textarea>
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
                       value="{{ old('penulis', $berita->penulis) }}" 
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
                
                @if($berita->gambar)
                    <div style="margin-top: 10px;">
                        <strong>Gambar Saat Ini:</strong>
                        <br>
                        <img src="{{ asset('storage/' . $berita->gambar) }}" 
                             alt="Gambar Berita" 
                             style="max-width: 200px; max-height: 150px; border-radius: var(--border-radius-sm); border: 2px solid var(--primary-light); margin-top: 8px;">
                    </div>
                @endif
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
                    <option value="semua" {{ old('target_berita', $berita->target_berita) == 'semua' ? 'selected' : '' }}>
                        Semua Santri
                    </option>
                    <option value="kelas_tertentu" {{ old('target_berita', $berita->target_berita) == 'kelas_tertentu' ? 'selected' : '' }}>
                        Kelas Tertentu
                    </option>
                    <option value="santri_tertentu" {{ old('target_berita', $berita->target_berita) == 'santri_tertentu' ? 'selected' : '' }}>
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
                    <option value="draft" {{ old('status', $berita->status) == 'draft' ? 'selected' : '' }}>
                        Draft (Belum Dipublikasi)
                    </option>
                    <option value="published" {{ old('status', $berita->status) == 'published' ? 'selected' : '' }}>
                        Published (Dipublikasi)
                    </option>
                </select>
                @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Section: Pilih Kelas Tertentu -->
        <div id="kelas-section" class="form-group" style="display: {{ old('target_berita', $berita->target_berita) == 'kelas_tertentu' ? 'block' : 'none' }};">
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
                                   {{ in_array($kelas, old('target_kelas', $berita->target_kelas ?? [])) ? 'checked' : '' }}>
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
                <span id="selected-kelas-count">{{ count(old('target_kelas', $berita->target_kelas ?? [])) }}</span> kelas dipilih dari {{ count($kelasOptions) }} total kelas.
            </small>
        </div>

        <!-- Section: Pilih Santri Tertentu -->
        <div id="santri-section" class="form-group" style="display: {{ old('target_berita', $berita->target_berita) == 'santri_tertentu' ? 'block' : 'none' }};">
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
                    <div style="background: white; padding: 12px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
                        <label style="display: flex; align-items: center; gap: 10px; margin: 0; cursor: pointer;">
                            <input type="checkbox" 
                                   id="santri_{{ $s->id_santri }}" 
                                   name="santri_tertentu[]" 
                                   value="{{ $s->id_santri }}" 
                                   class="santri-checkbox"
                                   style="width: 18px; height: 18px; flex-shrink: 0;"
                                   {{ in_array($s->id_santri, old('santri_tertentu', $selectedSantri)) ? 'checked' : '' }}>
                            
                            @if($s->foto_santri)
                                <img src="{{ asset('storage/santri/' . $s->foto_santri) }}" 
                                     alt="{{ $s->nama_santri }}" 
                                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-color);">
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">
                                    {{ strtoupper(substr($s->nama_santri, 0, 1)) }}
                                </div>
                            @endif
                            
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--primary-color); font-size: 0.85em;">
                                    {{ $s->id_santri }}
                                </div>
                                <div style="font-weight: 500; color: var(--text-color); font-size: 0.9em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $s->nama_santri }}
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
                <span id="selected-count">{{ count(old('santri_tertentu', $selectedSantri)) }}</span> santri dipilih dari {{ $santri->count() }} total santri aktif.
            </small>
        </div>

        <!-- Submit Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--primary-light);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Berita
            </button>
            <a href="{{ route('admin.berita.show', $berita->id_berita) }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> Lihat Berita
            </a>
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

    // Toggle sections
    targetBerita.addEventListener('change', function() {
        santriSection.style.display = 'none';
        kelasSection.style.display = 'none';
        
        if (this.value === 'santri_tertentu') {
            santriSection.style.display = 'block';
        } else if (this.value === 'kelas_tertentu') {
            kelasSection.style.display = 'block';
        } else {
            if (selectAll) selectAll.checked = false;
            santriCheckboxes.forEach(cb => cb.checked = false);
            kelasCheckboxes.forEach(cb => cb.checked = false);
            updateSelectedCount();
            updateSelectedKelasCount();
        }
    });

    // Select All
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            santriCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    // Individual checkboxes
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

    kelasCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedKelasCount);
    });

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.santri-checkbox:checked').length;
        if (selectedCount) selectedCount.textContent = checkedCount;
    }

    function updateSelectedKelasCount() {
        const checkedCount = document.querySelectorAll('.kelas-checkbox:checked').length;
        if (selectedKelasCount) selectedKelasCount.textContent = checkedCount;
    }

    // Initial setup
    const initialCheckedCount = document.querySelectorAll('.santri-checkbox:checked').length;
    if (selectAll) {
        selectAll.checked = initialCheckedCount === santriCheckboxes.length;
        selectAll.indeterminate = initialCheckedCount > 0 && initialCheckedCount < santriCheckboxes.length;
    }
    
    updateSelectedCount();
    updateSelectedKelasCount();
});
</script>
@endsection