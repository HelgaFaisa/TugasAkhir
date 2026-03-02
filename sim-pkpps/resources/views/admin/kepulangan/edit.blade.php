{{-- resources/views/admin/kepulangan/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Izin Kepulangan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Izin Kepulangan</h2>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-box">
    <form action="{{ route('admin.kepulangan.update', $kepulangan->id_kepulangan) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Info Kepulangan --}}
        <div style="background: #E8F7F2; padding: 15px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid #6FBA9D;">
            <p style="margin: 5px 0;"><strong>ID Kepulangan:</strong> {{ $kepulangan->id_kepulangan }}</p>
            <p style="margin: 5px 0;"><strong>Santri:</strong> {{ $kepulangan->santri->nama_lengkap }} ({{ $kepulangan->santri->id_santri }})</p>
            <p style="margin: 5px 0;"><strong>Status:</strong> 
                <span style="display: inline-block; background: #ffc107; color: #000; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">
                    {{ $kepulangan->status }}
                </span>
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 14px;">
            <div class="form-group">
                <label for="tanggal_pulang">
                    <i class="fas fa-calendar-alt form-icon"></i>
                    Tanggal Pulang <span style="color: #dc3545;">*</span>
                </label>
                <input type="date" 
                       name="tanggal_pulang" 
                       id="tanggal_pulang" 
                       class="form-control" 
                       value="{{ old('tanggal_pulang', $kepulangan->tanggal_pulang->format('Y-m-d')) }}"
                       min="{{ date('Y-m-d') }}"
                       required>
            </div>
            
            <div class="form-group">
                <label for="tanggal_kembali">
                    <i class="fas fa-calendar-check form-icon"></i>
                    Tanggal Kembali <span style="color: #dc3545;">*</span>
                </label>
                <input type="date" 
                       name="tanggal_kembali" 
                       id="tanggal_kembali" 
                       class="form-control" 
                       value="{{ old('tanggal_kembali', $kepulangan->tanggal_kembali->format('Y-m-d')) }}"
                       required>
            </div>
        </div>

        {{-- Info Durasi --}}
        <div id="durasiInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid #FF8B94;">
            <p style="margin: 5px 0;"><strong>Durasi Izin:</strong> <span id="durasiHari" style="display: inline-block; background: #007bff; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">{{ $kepulangan->durasi_izin }} hari</span></p>
        </div>

        <div class="form-group">
            <label for="alasan">
                <i class="fas fa-comment-alt form-icon"></i>
                Alasan Kepulangan <span style="color: #dc3545;">*</span>
            </label>
            <textarea name="alasan" 
                      id="alasan" 
                      class="form-control" 
                      rows="4" 
                      placeholder="Jelaskan alasan kepulangan"
                      required>{{ old('alasan', $kepulangan->alasan) }}</textarea>
            <small style="color: #7F8C8D; margin-top: 5px; display: block;">
                <span id="charCount">{{ strlen(old('alasan', $kepulangan->alasan)) }}</span>/500 karakter
            </small>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.kepulangan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Calculate durasi when dates change
document.getElementById('tanggal_pulang').addEventListener('change', calculateDurasi);
document.getElementById('tanggal_kembali').addEventListener('change', calculateDurasi);

function calculateDurasi() {
    const tanggalPulang = document.getElementById('tanggal_pulang').value;
    const tanggalKembali = document.getElementById('tanggal_kembali').value;
    
    if (!tanggalPulang || !tanggalKembali) return;
    
    const startDate = new Date(tanggalPulang);
    const endDate = new Date(tanggalKembali);
    
    if (endDate <= startDate) return;
    
    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    
    const durasiColor = diffDays > 7 ? '#ffc107' : '#007bff';
    const textColor = diffDays > 7 ? '#000' : 'white';
    
    document.getElementById('durasiHari').textContent = diffDays + ' hari';
    document.getElementById('durasiHari').style.background = durasiColor;
    document.getElementById('durasiHari').style.color = textColor;
}

// Character counter (PERBAIKAN: Tidak ada validasi minimal)
document.getElementById('alasan').addEventListener('input', function() {
    const current = this.value.length;
    const counter = document.getElementById('charCount');
    counter.textContent = current;
    
    if (current > 500) {
        counter.style.color = 'red';
    } else {
        counter.style.color = '#7F8C8D';
    }
});

// Auto-set minimum tanggal_kembali
document.getElementById('tanggal_pulang').addEventListener('change', function() {
    const pulangDate = new Date(this.value);
    pulangDate.setDate(pulangDate.getDate() + 1);
    
    const minKembaliDate = pulangDate.toISOString().split('T')[0];
    document.getElementById('tanggal_kembali').min = minKembaliDate;
    
    const currentKembali = document.getElementById('tanggal_kembali').value;
    if (currentKembali && currentKembali <= this.value) {
        document.getElementById('tanggal_kembali').value = minKembaliDate;
    }
});

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    calculateDurasi();
    
    const alasanField = document.getElementById('alasan');
    document.getElementById('charCount').textContent = alasanField.value.length;
});
</script>
@endsection