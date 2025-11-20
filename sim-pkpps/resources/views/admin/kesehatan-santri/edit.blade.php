@extends('layouts.app')

@section('title', 'Edit Data Kesehatan Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Data Kesehatan Santri</h2>
</div>

<!-- Content Box -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-file-medical"></i> Form Edit Data Kesehatan
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.kesehatan-santri.show', $kesehatanSantri) }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> Lihat Detail
            </a>
            <a href="{{ route('admin.kesehatan-santri.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('admin.kesehatan-santri.update', $kesehatanSantri) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Pilih Santri (Disabled) -->
        <div class="form-group">
            <label for="id_santri"><i class="fas fa-user form-icon"></i>Santri *</label>
            <select name="id_santri" id="id_santri" class="form-control @error('id_santri') is-invalid @enderror" disabled>
                <option value="{{ $kesehatanSantri->id_santri }}">
                    {{ $kesehatanSantri->id_santri }} - {{ $kesehatanSantri->santri->nama_lengkap }} ({{ $kesehatanSantri->santri->kelas }})
                </option>
            </select>
            <input type="hidden" name="id_santri" value="{{ $kesehatanSantri->id_santri }}">
            @error('id_santri')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text">
                <i class="fas fa-info-circle"></i> Santri tidak dapat diubah setelah data disimpan
            </small>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Tanggal Masuk -->
            <div class="form-group">
                <label for="tanggal_masuk"><i class="fas fa-calendar-plus form-icon"></i>Tanggal Masuk UKP *</label>
                <input type="date" 
                       name="tanggal_masuk" 
                       id="tanggal_masuk" 
                       class="form-control @error('tanggal_masuk') is-invalid @enderror"
                       value="{{ old('tanggal_masuk', $kesehatanSantri->tanggal_masuk->format('Y-m-d')) }}" 
                       max="{{ date('Y-m-d') }}"
                       required>
                @error('tanggal_masuk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status"><i class="fas fa-info-circle form-icon"></i>Status *</label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="dirawat" {{ old('status', $kesehatanSantri->status) == 'dirawat' ? 'selected' : '' }}>Dirawat</option>
                    <option value="sembuh" {{ old('status', $kesehatanSantri->status) == 'sembuh' ? 'selected' : '' }}>Sembuh</option>
                    <option value="izin" {{ old('status', $kesehatanSantri->status) == 'izin' ? 'selected' : '' }}>Izin Pulang</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Tanggal Keluar -->
        <div id="tanggal_keluar_group" class="form-group">
            <label for="tanggal_keluar"><i class="fas fa-calendar-check form-icon"></i>Tanggal Keluar UKP</label>
            <input type="date" 
                   name="tanggal_keluar" 
                   id="tanggal_keluar" 
                   class="form-control @error('tanggal_keluar') is-invalid @enderror"
                   value="{{ old('tanggal_keluar', $kesehatanSantri->tanggal_keluar ? $kesehatanSantri->tanggal_keluar->format('Y-m-d') : '') }}" 
                   max="{{ date('Y-m-d') }}">
            @error('tanggal_keluar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text">
                <i class="fas fa-info-circle"></i> Kosongkan jika santri masih dirawat
            </small>
        </div>

        <!-- Keluhan -->
        <div class="form-group">
            <label for="keluhan"><i class="fas fa-notes-medical form-icon"></i>Keluhan *</label>
            <textarea name="keluhan" 
                      id="keluhan" 
                      rows="4" 
                      class="form-control @error('keluhan') is-invalid @enderror"
                      placeholder="Tuliskan keluhan atau gejala yang dialami santri..."
                      required>{{ old('keluhan', $kesehatanSantri->keluhan) }}</textarea>
            @error('keluhan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text">Maksimal 1000 karakter</small>
        </div>

        <!-- Catatan -->
        <div class="form-group">
            <label for="catatan"><i class="fas fa-clipboard form-icon"></i>Catatan Petugas</label>
            <textarea name="catatan" 
                      id="catatan" 
                      rows="3" 
                      class="form-control @error('catatan') is-invalid @enderror"
                      placeholder="Catatan tambahan dari petugas kesehatan...">{{ old('catatan', $kesehatanSantri->catatan) }}</textarea>
            @error('catatan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text">Maksimal 1000 karakter (opsional)</small>
        </div>

        <!-- Buttons -->
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px;">
            <a href="{{ route('admin.kesehatan-santri.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Data
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const tanggalKeluarGroup = document.getElementById('tanggal_keluar_group');
    const tanggalKeluarInput = document.getElementById('tanggal_keluar');
    const tanggalMasukInput = document.getElementById('tanggal_masuk');
    
    // Function to toggle tanggal keluar visibility
    function toggleTanggalKeluar() {
        if (statusSelect.value === 'dirawat') {
            tanggalKeluarGroup.style.display = 'none';
            tanggalKeluarInput.value = '';
            tanggalKeluarInput.removeAttribute('required');
        } else {
            tanggalKeluarGroup.style.display = 'block';
            if (statusSelect.value === 'sembuh' || statusSelect.value === 'izin') {
                tanggalKeluarInput.setAttribute('required', 'required');
            }
        }
    }
    
    // Set minimum date for tanggal_keluar based on tanggal_masuk
    function setMinTanggalKeluar() {
        if (tanggalMasukInput.value) {
            tanggalKeluarInput.min = tanggalMasukInput.value;
        }
    }
    
    // Event listeners
    statusSelect.addEventListener('change', toggleTanggalKeluar);
    tanggalMasukInput.addEventListener('change', setMinTanggalKeluar);
    
    // Initialize on page load
    toggleTanggalKeluar();
    setMinTanggalKeluar();
    
    // Character counter
    function setupCharacterCounter(textareaId, maxLength) {
        const textarea = document.getElementById(textareaId);
        const counter = document.createElement('div');
        counter.style.cssText = 'text-align: right; font-size: 0.85em; color: #7F8C8D; margin-top: 5px;';
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length}/${maxLength} karakter`;
            counter.style.color = remaining < 50 ? '#E74C3C' : '#7F8C8D';
        }
        
        textarea.addEventListener('input', updateCounter);
        
        const lastSibling = textarea.parentNode.lastElementChild;
        if (lastSibling.classList && lastSibling.classList.contains('form-text')) {
            lastSibling.parentNode.appendChild(counter);
        } else {
            textarea.parentNode.appendChild(counter);
        }
        
        updateCounter();
    }
    
    setupCharacterCounter('keluhan', 1000);
    setupCharacterCounter('catatan', 1000);
});
</script>

@endsection