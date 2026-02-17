@extends('layouts.app')

@section('title', 'Tambah Berita Baru')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Berita Baru</h2>
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
    <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data" id="beritaForm">
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
                      required>{{ old('konten') }}</textarea>
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
                                   id="kelas_{{ $kelas->id }}" 
                                   name="target_kelas[]" 
                                   value="{{ $kelas->id }}" 
                                   class="kelas-checkbox"
                                   style="margin-right: 10px; width: 18px; height: 18px;"
                                   {{ in_array($kelas->id, old('target_kelas', [])) ? 'checked' : '' }}>
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
                <span id="selected-kelas-count">0</span> kelas dipilih dari {{ $kelasOptions->count() }} total kelas.
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

    // Load existing content (old values)
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

    // Initial state
    if (targetBerita.value === 'kelas_tertentu') {
        kelasSection.style.display = 'block';
    }

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
.ql-container { font-size: 14px; font-family: Arial, sans-serif; min-height: 250px; }
.ql-editor { min-height: 250px; max-height: 500px; overflow-y: auto; }
.ql-editor h1 { font-size: 2em; color: #2c3e50; }
.ql-editor h2 { font-size: 1.5em; color: #34495e; }
.ql-editor h3 { font-size: 1.2em; color: #34495e; }
.ql-editor p { margin-bottom: 1em; }
.ql-editor ol, .ql-editor ul { padding-left: 1.5em; margin-bottom: 1em; }
.ql-editor li { margin-bottom: 0.5em; }
</style>
@endsection
