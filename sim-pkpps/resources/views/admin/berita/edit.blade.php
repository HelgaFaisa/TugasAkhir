@extends('layouts.app')

@section('title', 'Edit Berita')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Berita</h2>
</div>

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
    <form action="{{ route('admin.berita.update', $berita->id_berita) }}" method="POST" enctype="multipart/form-data" id="beritaForm">
        @csrf
        @method('PUT')
        
        <!-- ID Berita (Read-only) -->
        <div style="background: var(--primary-light); padding: 15px; border-radius: var(--border-radius-sm); margin-bottom: 14px;">
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

        <!-- Konten Berita (Quill Editor) -->
        <div class="form-group">
            <label for="konten">
                <i class="fas fa-file-alt form-icon"></i>
                Konten Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <div id="editor-container" style="min-height: 300px; background: white; border: 1px solid #ddd; border-radius: 4px;"></div>
            <textarea name="konten" 
                      id="konten" 
                      class="form-control @error('konten') is-invalid @enderror"
                      style="display: none;" 
                      required>{{ old('konten', $berita->konten) }}</textarea>
            @error('konten')
                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
            @enderror
            <span class="form-text">
                <i class="fas fa-magic"></i> Gunakan toolbar untuk formatting: Bold, Italic, Daftar, Warna, dsb.
            </span>
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
        @php
            $selectedKelas = old('target_kelas', $berita->target_kelas ?? []);
        @endphp
        <div id="kelas-section" class="form-group" style="display: {{ old('target_berita', $berita->target_berita) == 'kelas_tertentu' ? 'block' : 'none' }};">
            <label>
                <i class="fas fa-graduation-cap form-icon"></i>
                Pilih Kelas yang Akan Menerima Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <div style="border: 2px solid var(--primary-light); border-radius: var(--border-radius-sm); padding: 14px; background-color: var(--primary-light);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 11px;">
                    @foreach($kelasOptions as $kelas)
                    <div style="background: white; padding: 12px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
                        <label style="display: flex; align-items: center; margin: 0; cursor: pointer;">
                            <input type="checkbox" 
                                   id="kelas_{{ $kelas->id }}" 
                                   name="target_kelas[]" 
                                   value="{{ $kelas->id }}" 
                                   class="kelas-checkbox"
                                   style="margin-right: 10px; width: 18px; height: 18px;"
                                   {{ in_array($kelas->id, $selectedKelas) ? 'checked' : '' }}>
                            <span style="font-weight: 600; color: var(--text-color);">
                                {{ $kelas->nama_kelas }}
                            </span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <small class="form-text">
                <i class="fas fa-info-circle"></i>
                <span id="selected-kelas-count">{{ count($selectedKelas) }}</span> kelas dipilih dari {{ $kelasOptions->count() }} total kelas.
            </small>
        </div>

        <!-- Submit Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 22px; padding-top: 20px; border-top: 2px solid var(--primary-light);">
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

<!-- Quill Editor CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quill Editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Tulis konten berita di sini...'
    });

    // Load existing content
    var existing = document.getElementById('konten').value;
    if (existing) quill.root.innerHTML = existing;

    // Sync on change
    quill.on('text-change', function() {
        document.getElementById('konten').value = quill.root.innerHTML;
    });

    // Sync on submit + validate
    document.getElementById('beritaForm').onsubmit = function() {
        document.getElementById('konten').value = quill.root.innerHTML;
        if (quill.getText().trim().length === 0) {
            alert('Konten berita tidak boleh kosong!');
            return false;
        }
        return true;
    };

    // Target berita toggle
    var targetBerita = document.getElementById('target_berita');
    var kelasSection = document.getElementById('kelas-section');
    var kelasCheckboxes = document.querySelectorAll('.kelas-checkbox');

    targetBerita.addEventListener('change', function() {
        kelasSection.style.display = this.value === 'kelas_tertentu' ? 'block' : 'none';
        if (this.value !== 'kelas_tertentu') {
            kelasCheckboxes.forEach(function(cb) { cb.checked = false; });
            updateKelasCount();
        }
    });

    // Kelas counter
    kelasCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', updateKelasCount);
    });

    function updateKelasCount() {
        var count = document.querySelectorAll('.kelas-checkbox:checked').length;
        var el = document.getElementById('selected-kelas-count');
        if (el) el.textContent = count;
    }

    updateKelasCount();
});
</script>

<style>
.ql-toolbar { background-color: #f8f9fa; border-radius: 4px 4px 0 0; border-bottom: 2px solid #dee2e6; }
.ql-container { font-size: 11px; font-family: Arial, sans-serif; min-height: 250px; }
.ql-editor { min-height: 250px; max-height: 500px; overflow-y: auto; }
.ql-editor h1 { font-size: 2em; color: #2c3e50; }
.ql-editor h2 { font-size: 1.5em; color: #34495e; }
.ql-editor h3 { font-size: 1.2em; color: #34495e; }
.ql-editor p { margin-bottom: 1em; }
.ql-editor ol, .ql-editor ul { padding-left: 1.5em; margin-bottom: 1em; }
.ql-editor li { margin-bottom: 0.5em; }
</style>
@endsection
