@extends('layouts.app')

@section('title', 'Edit Pembinaan & Sanksi')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Pembinaan & Sanksi</h2>
</div>

<div class="content-box">
    <div class="alert alert-info" style="margin-bottom: 25px;">
        <i class="fas fa-info-circle"></i>
        <strong>Edit Konten:</strong> Gunakan editor di bawah untuk mengubah konten dengan format yang rapi.
    </div>

    <form action="{{ route('admin.pembinaan-sanksi.update', $pembinaan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>
                <i class="fas fa-id-card form-icon"></i>
                ID Pembinaan
            </label>
            <input type="text" class="form-control" value="{{ $pembinaan->id_pembinaan }}" disabled>
        </div>

        <div class="form-group">
            <label for="judul">
                <i class="fas fa-heading form-icon"></i>
                Judul <span style="color: var(--danger-color);">*</span>
            </label>
            <input type="text" 
                   name="judul" 
                   id="judul"
                   class="form-control @error('judul') is-invalid @enderror"
                   value="{{ old('judul', $pembinaan->judul) }}"
                   placeholder="Contoh: PEMBINAAN DAN SANKSI"
                   required>
            @error('judul')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="konten">
                <i class="fas fa-file-alt form-icon"></i>
                Konten <span style="color: var(--danger-color);">*</span>
            </label>
            <div id="editor-container" style="min-height: 400px; background: white; border: 1px solid #ddd; border-radius: 4px;"></div>
            <textarea name="konten" 
                      id="konten"
                      class="form-control @error('konten') is-invalid @enderror"
                      style="display: none;"
                      required>{{ old('konten', $pembinaan->konten) }}</textarea>
            @error('konten')
                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
            @enderror
            <span class="form-text">
                <i class="fas fa-magic"></i> Gunakan toolbar di atas untuk formatting
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="urutan">
                    <i class="fas fa-sort-numeric-up form-icon"></i>
                    Urutan Tampilan
                </label>
                <input type="number" 
                       name="urutan" 
                       id="urutan"
                       class="form-control @error('urutan') is-invalid @enderror"
                       value="{{ old('urutan', $pembinaan->urutan) }}"
                       min="0">
                @error('urutan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-toggle-on form-icon"></i>
                    Status
                </label>
                <div style="margin-top: 12px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $pembinaan->is_active) ? 'checked' : '' }}
                               style="margin-right: 8px;">
                        <span>Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="btn-group" style="margin-top: 22px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.pembinaan-sanksi.show', $pembinaan) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Lihat Detail
            </a>
            <a href="{{ route('admin.pembinaan-sanksi.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // Initialize Quill Editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        },
        placeholder: 'Edit konten di sini...'
    });

    // Load existing content
    var existingContent = document.getElementById('konten').value;
    if (existingContent) {
        quill.root.innerHTML = existingContent;
    }

    // Sync Quill content to hidden textarea on form submit
    document.querySelector('form').onsubmit = function() {
        var kontenInput = document.getElementById('konten');
        kontenInput.value = quill.root.innerHTML;
        
        // Validation: check if content is empty
        if (quill.getText().trim().length === 0) {
            alert('Konten tidak boleh kosong!');
            return false;
        }
        
        return true;
    };

    // Optional: Sync on every change (real-time)
    quill.on('text-change', function() {
        document.getElementById('konten').value = quill.root.innerHTML;
    });
</script>

<style>
    /* Custom Quill Editor Styling */
    .ql-toolbar {
        background-color: #f8f9fa;
        border-radius: 4px 4px 0 0;
        border-bottom: 2px solid #dee2e6;
    }
    
    .ql-container {
        font-size: 11px;
        font-family: Arial, sans-serif;
        min-height: 350px;
    }
    
    .ql-editor {
        min-height: 350px;
        max-height: 600px;
        overflow-y: auto;
    }
    
    .ql-editor h1 {
        font-size: 2em;
        color: #2c3e50;
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    
    .ql-editor h2 {
        font-size: 1.5em;
        color: #34495e;
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    
    .ql-editor h3 {
        font-size: 1.2em;
        color: #34495e;
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    
    .ql-editor p {
        margin-bottom: 1em;
    }
    
    .ql-editor ol, .ql-editor ul {
        padding-left: 1.5em;
        margin-bottom: 1em;
    }
    
    .ql-editor li {
        margin-bottom: 0.5em;
    }
</style>
@endsection