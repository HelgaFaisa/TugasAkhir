{{-- resources/views/admin/kepulangan/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Tambah Izin Kepulangan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Izin Kepulangan</h2>
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
    <form action="{{ route('admin.kepulangan.store') }}" method="POST" id="kepulanganForm">
        @csrf
        
        <div class="form-group">
            <label for="id_santri">
                <i class="fas fa-user form-icon"></i>
                Pilih Santri <span style="color: #dc3545;">*</span>
            </label>
            <select name="id_santri" id="id_santri" class="form-control" required>
                <option value="">-- Pilih Santri --</option>
                @foreach($santriList as $santri)
                    <option value="{{ $santri->id_santri }}" {{ old('id_santri') == $santri->id_santri ? 'selected' : '' }}>
                        {{ $santri->nama_lengkap }} ({{ $santri->id_santri }} - {{ $santri->kelas }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Info Santri --}}
        <div id="santriInfo" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #6FBA9D;">
            <h4 style="margin-top: 0; color: #2C3E50;">Informasi Santri</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div>
                    <p style="margin: 5px 0;"><strong>Nama:</strong> <span id="santriNama">-</span></p>
                    <p style="margin: 5px 0;"><strong>Kelas:</strong> <span id="santriKelas">-</span></p>
                </div>
                <div>
                    <p style="margin: 5px 0;"><strong>Total Hari Izin Tahun Ini:</strong> <span id="totalHariIzin" style="display: inline-block; background: #81C6E8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">0 hari</span></p>
                    <p style="margin: 5px 0;"><strong>Sisa Kuota:</strong> <span id="sisaKuota" style="display: inline-block; background: #6FBA9D; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">12 hari</span></p>
                </div>
            </div>
            <div id="warningOverLimit" style="display: none; margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; color: #856404;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Peringatan:</strong> Santri ini sudah melebihi batas 12 hari per tahun!
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="tanggal_pulang">
                    <i class="fas fa-calendar-alt form-icon"></i>
                    Tanggal Pulang <span style="color: #dc3545;">*</span>
                </label>
                <input type="date" 
                       name="tanggal_pulang" 
                       id="tanggal_pulang" 
                       class="form-control" 
                       value="{{ old('tanggal_pulang', date('Y-m-d')) }}"
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
                       value="{{ old('tanggal_kembali') }}"
                       required>
            </div>
        </div>

        {{-- Info Durasi --}}
        <div id="durasiInfo" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #FF8B94;">
            <h4 style="margin-top: 0; color: #2C3E50;">Informasi Durasi Izin</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <p style="margin: 5px 0;"><strong>Durasi Izin:</strong> <span id="durasiHari" style="display: inline-block; background: #007bff; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">0 hari</span></p>
                </div>
                <div>
                    <p style="margin: 5px 0;"><strong>Total Setelah Izin:</strong> <span id="totalSetelahIzin" style="display: inline-block; background: #6c757d; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">0 hari</span></p>
                </div>
                <div>
                    <p style="margin: 5px 0;"><strong>Sisa Kuota Setelah Izin:</strong> <span id="sisaKuotaSetelah" style="display: inline-block; background: #6FBA9D; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">12 hari</span></p>
                </div>
            </div>
            <div id="warningDurasi" style="display: none; margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; color: #856404;">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="warningMessage"></span>
            </div>
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
                      placeholder="Jelaskan alasan kepulangan secara detail (minimal 10 karakter)"
                      required>{{ old('alasan') }}</textarea>
            <small style="color: #7F8C8D; margin-top: 5px; display: block;">
                <span id="charCount">0</span>/500 karakter
            </small>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Simpan Izin Kepulangan
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Reset Form
            </button>
            <a href="{{ route('admin.kepulangan.index') }}" class="btn btn-danger">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

{{-- Modal Konfirmasi Over Limit --}}
<div class="modal fade" id="overLimitModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0; color: #2C3E50;">Konfirmasi Izin Melebihi Batas</h3>
            </div>
            <div style="padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #856404; margin-bottom: 10px;"></i>
                <h4 style="margin: 10px 0; color: #856404;">Peringatan!</h4>
                <p id="overLimitMessage" style="margin: 0; color: #856404;"></p>
            </div>
            <p>Apakah Anda yakin ingin melanjutkan pengajuan izin ini?</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('overLimitModal')">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmOverLimit">
                    <i class="fas fa-check"></i> Lanjutkan Tetap
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 500px; width: 90%; margin: auto; }
.modal-content { max-height: 90vh; overflow-y: auto; }
</style>

<script>
let currentSantriData = null;
let isOverLimit = false;

// Load santri data when selected
document.getElementById('id_santri').addEventListener('change', function() {
    const santriId = this.value;
    
    if (!santriId) {
        document.getElementById('santriInfo').style.display = 'none';
        currentSantriData = null;
        calculateDurasi();
        return;
    }

    const infoDiv = document.getElementById('santriInfo');
    infoDiv.style.display = 'block';
    infoDiv.innerHTML = '<div style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Memuat data santri...</div>';

    fetch(`/admin/api/kepulangan/santri/${santriId}`)
        .then(response => response.json())
        .then(data => {
            currentSantriData = data;
            updateSantriInfo(data);
            calculateDurasi();
        })
        .catch(error => {
            infoDiv.innerHTML = `<div style="padding: 15px; background: #ffe8ea; border: 1px solid #ffd5d8; border-radius: 6px; color: #7C2D35;">Error loading santri data: ${error.message}</div>`;
        });
});

function updateSantriInfo(data) {
    const santri = data.santri;
    const penggunaan = data.penggunaan_izin;
    
    const totalColor = penggunaan.total_hari > 8 ? '#ffc107' : '#81C6E8';
    const sisaColor = penggunaan.sisa_kuota <= 3 ? '#dc3545' : '#6FBA9D';
    
    document.getElementById('santriInfo').innerHTML = `
        <h4 style="margin-top: 0; color: #2C3E50;">Informasi Santri</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <p style="margin: 5px 0;"><strong>Nama:</strong> ${santri.nama_lengkap}</p>
                <p style="margin: 5px 0;"><strong>Kelas:</strong> ${santri.kelas}</p>
            </div>
            <div>
                <p style="margin: 5px 0;"><strong>Total Hari Izin Tahun Ini:</strong> <span style="display: inline-block; background: ${totalColor}; color: ${penggunaan.total_hari > 8 ? '#000' : 'white'}; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">${penggunaan.total_hari} hari</span></p>
                <p style="margin: 5px 0;"><strong>Sisa Kuota:</strong> <span style="display: inline-block; background: ${sisaColor}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">${penggunaan.sisa_kuota} hari</span></p>
            </div>
        </div>
        ${penggunaan.over_limit ? `
        <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; color: #856404;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Peringatan:</strong> Santri ini sudah melebihi batas 12 hari per tahun!
        </div>
        ` : ''}
    `;
}

// Calculate durasi when dates change
document.getElementById('tanggal_pulang').addEventListener('change', calculateDurasi);
document.getElementById('tanggal_kembali').addEventListener('change', calculateDurasi);

function calculateDurasi() {
    const tanggalPulang = document.getElementById('tanggal_pulang').value;
    const tanggalKembali = document.getElementById('tanggal_kembali').value;
    const durasiInfoDiv = document.getElementById('durasiInfo');
    
    if (!tanggalPulang || !tanggalKembali) {
        durasiInfoDiv.style.display = 'none';
        return;
    }
    
    const startDate = new Date(tanggalPulang);
    const endDate = new Date(tanggalKembali);
    
    if (endDate <= startDate) {
        durasiInfoDiv.style.display = 'none';
        return;
    }
    
    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    
    durasiInfoDiv.style.display = 'block';
    
    let totalSetelah = diffDays;
    let sisaSetelah = 12 - diffDays;
    let showWarning = false;
    let warningMessage = '';
    
    if (currentSantriData) {
        const currentUsage = currentSantriData.penggunaan_izin.total_hari;
        totalSetelah = currentUsage + diffDays;
        sisaSetelah = 12 - totalSetelah;
        
        if (totalSetelah > 12) {
            showWarning = true;
            warningMessage = `Izin ini akan melebihi batas 12 hari per tahun (total: ${totalSetelah} hari)`;
            isOverLimit = true;
        } else {
            isOverLimit = false;
        }
    }
    
    const durasiColor = diffDays > 7 ? '#ffc107' : '#007bff';
    const totalColor = totalSetelah > 12 ? '#dc3545' : '#6c757d';
    const sisaColor = sisaSetelah < 0 ? '#dc3545' : sisaSetelah <= 3 ? '#ffc107' : '#6FBA9D';
    
    durasiInfoDiv.innerHTML = `
        <h4 style="margin-top: 0; color: #2C3E50;">Informasi Durasi Izin</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <p style="margin: 5px 0;"><strong>Durasi Izin:</strong> <span style="display: inline-block; background: ${durasiColor}; color: ${diffDays > 7 ? '#000' : 'white'}; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">${diffDays} hari</span></p>
            </div>
            <div>
                <p style="margin: 5px 0;"><strong>Total Setelah Izin:</strong> <span style="display: inline-block; background: ${totalColor}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">${totalSetelah} hari</span></p>
            </div>
            <div>
                <p style="margin: 5px 0;"><strong>Sisa Kuota Setelah Izin:</strong> <span style="display: inline-block; background: ${sisaColor}; color: ${sisaSetelah <= 3 && sisaSetelah >= 0 ? '#000' : 'white'}; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">${Math.max(0, sisaSetelah)} hari</span></p>
            </div>
        </div>
        ${showWarning ? `
        <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; color: #856404;">
            <i class="fas fa-exclamation-triangle"></i>
            ${warningMessage}
        </div>
        ` : ''}
    `;
}

// Character counter for alasan
document.getElementById('alasan').addEventListener('input', function() {
    const current = this.value.length;
    const counter = document.getElementById('charCount');
    counter.textContent = current;
    
    if (current > 500) {
        counter.style.color = 'red';
    } else if (current < 10) {
        counter.style.color = 'orange';
    } else {
        counter.style.color = 'green';
    }
});

// Form submission with over limit confirmation
document.getElementById('kepulanganForm').addEventListener('submit', function(e) {
    if (isOverLimit) {
        e.preventDefault();
        
        const warningDiv = document.querySelector('#durasiInfo div[style*="background: #fff3cd"]');
        const message = warningDiv ? warningDiv.textContent.trim() : 'Izin ini melebihi batas 12 hari per tahun';
        document.getElementById('overLimitMessage').textContent = message;
        
        document.getElementById('overLimitModal').style.display = 'flex';
    }
});

// Confirm over limit submission
document.getElementById('confirmOverLimit').addEventListener('click', function() {
    closeModal('overLimitModal');
    isOverLimit = false;
    document.getElementById('kepulanganForm').submit();
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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_pulang').min = today;
    
    const alasanField = document.getElementById('alasan');
    document.getElementById('charCount').textContent = alasanField.value.length;
    
    calculateDurasi();
});

// Form reset handler
document.querySelector('button[type="reset"]').addEventListener('click', function() {
    setTimeout(() => {
        document.getElementById('santriInfo').style.display = 'none';
        document.getElementById('durasiInfo').style.display = 'none';
        currentSantriData = null;
        isOverLimit = false;
        document.getElementById('charCount').textContent = '0';
    }, 100);
});

// Helper functions
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.fade').forEach(modal => modal.style.display = 'none');
    }
});

// Close modal on outside click
document.querySelectorAll('.modal.fade').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>
@endsection