{{-- resources/views/admin/kepulangan/settings.blade.php --}}

@extends('layouts.app')

@section('title', 'Pengaturan Kuota Kepulangan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-cog"></i> Pengaturan Kuota Kepulangan</h2>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

{{-- Statistik Periode Saat Ini --}}
<div class="row-cards">
    <div class="card card-info">
        <h3>Total Santri Aktif</h3>
        <div class="card-value">{{ $totalSantri }}</div>
        <i class="fas fa-users card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Total Izin Periode Ini</h3>
        <div class="card-value">{{ $totalIzinPeriodeIni }}</div>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Santri Over Limit</h3>
        <div class="card-value">{{ count($santriOverLimit) }}</div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
        @if(count($santriOverLimit) > 0)
            <a href="{{ route('admin.kepulangan.over-limit') }}" style="font-size: 0.85rem; color: #dc3545; text-decoration: underline; margin-top: 5px; display: block;">
                Lihat Detail
            </a>
        @endif
    </div>
    <div class="card card-success">
        <h3>Terakhir Reset</h3>
        <div class="card-value" style="font-size: 1.2rem;">
            {{ $settings->terakhir_reset ? $settings->terakhir_reset->format('d M Y') : 'Belum Pernah' }}
        </div>
        <i class="fas fa-history card-icon"></i>
        @if($settings->reset_by)
            <small style="display: block; margin-top: 5px;">oleh {{ $settings->reset_by }}</small>
        @endif
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
    {{-- Settings Form --}}
    <div class="content-box">
        <h3 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
            <i class="fas fa-sliders-h"></i> Pengaturan Kuota
        </h3>
        
        <form action="{{ route('admin.kepulangan.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="kuota_maksimal">
                    <i class="fas fa-calculator"></i> Kuota Maksimal (Hari/Tahun)
                    <span style="color: #dc3545;">*</span>
                </label>
                <input type="number" 
                       name="kuota_maksimal" 
                       id="kuota_maksimal" 
                       class="form-control" 
                       value="{{ old('kuota_maksimal', $settings->kuota_maksimal) }}"
                       min="1"
                       max="365"
                       required>
                <small style="color: #7F8C8D; display: block; margin-top: 5px;">
                    Maksimal hari izin yang diperbolehkan per tahun (1-365 hari)
                </small>
            </div>
            
            <div class="form-group">
                <label for="periode_mulai">
                    <i class="fas fa-calendar-alt"></i> Tanggal Mulai Periode
                    <span style="color: #dc3545;">*</span>
                </label>
                <input type="date" 
                       name="periode_mulai" 
                       id="periode_mulai" 
                       class="form-control" 
                       value="{{ old('periode_mulai', $settings->periode_mulai->format('Y-m-d')) }}"
                       required>
            </div>
            
            <div class="form-group">
                <label for="periode_akhir">
                    <i class="fas fa-calendar-check"></i> Tanggal Akhir Periode
                    <span style="color: #dc3545;">*</span>
                </label>
                <input type="date" 
                       name="periode_akhir" 
                       id="periode_akhir" 
                       class="form-control" 
                       value="{{ old('periode_akhir', $settings->periode_akhir->format('Y-m-d')) }}"
                       required>
            </div>
            
            <div style="background: #E8F7F2; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #6FBA9D;">
                <strong>ℹ️ Informasi:</strong>
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    <li>Periode ini menentukan rentang waktu perhitungan kuota</li>
                    <li>Perubahan periode akan mempengaruhi perhitungan kuota santri</li>
                    <li>Gunakan fitur reset jika ingin memulai periode baru</li>
                </ul>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
        </form>
    </div>

    {{-- Reset Actions --}}
    <div class="content-box">
        <h3 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #FF8B94; padding-bottom: 10px;">
            <i class="fas fa-sync-alt"></i> Reset Kuota
        </h3>
        
        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
            <strong>⚠️ PERHATIAN:</strong>
            <p style="margin: 10px 0 0 0;">
                Reset kuota akan mengubah status semua izin yang "Disetujui" dalam periode saat ini menjadi "Selesai". 
                Ini akan mereset perhitungan kuota untuk memulai periode baru.
            </p>
        </div>

        {{-- Reset Semua Santri --}}
        <div style="background: #ffebee; padding: 20px; border-radius: 8px; border: 2px solid #dc3545; margin-bottom: 20px;">
            <h4 style="margin: 0 0 10px 0; color: #dc3545;">
                <i class="fas fa-users"></i> Reset Kuota Semua Santri
            </h4>
            <p style="color: #7F8C8D; font-size: 0.9rem; margin-bottom: 15px;">
                Mereset kuota untuk {{ $totalSantri }} santri aktif. Gunakan di awal tahun ajaran atau periode baru.
            </p>
            
            <form id="resetSemuaForm">
                @csrf
                <div class="form-group">
                    <label for="catatan_reset_semua">Catatan Reset (Opsional):</label>
                    <textarea name="catatan" 
                              id="catatan_reset_semua" 
                              class="form-control" 
                              rows="2" 
                              placeholder="Contoh: Reset untuk tahun ajaran 2025/2026"></textarea>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="konfirmasi" id="konfirmasi_reset_semua" required style="margin-right: 10px; width: 18px; height: 18px;">
                        <span>Saya memahami bahwa tindakan ini akan mereset kuota untuk <strong>SEMUA santri</strong></span>
                    </label>
                </div>
                
                <button type="button" onclick="confirmResetSemua()" class="btn btn-danger" style="width: 100%;">
                    <i class="fas fa-sync-alt"></i> Reset Kuota Semua Santri
                </button>
            </form>
        </div>

        {{-- Info Tambahan --}}
        <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; border-left: 4px solid #2196f3;">
            <strong>💡 Tips:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0; font-size: 0.9rem;">
                <li>Reset individual dapat dilakukan dari halaman detail santri</li>
                <li>Reset massal sebaiknya dilakukan di akhir periode</li>
                <li>Semua aktivitas reset tercatat dalam log history</li>
                <li>Data izin lama tetap tersimpan untuk arsip</li>
            </ul>
        </div>
    </div>
</div>

{{-- History Reset Logs --}}
<div class="content-box" style="margin-top: 20px;">
    <h3 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
        <i class="fas fa-history"></i> History Reset Kuota
    </h3>
    
    @if($resetLogs->count() > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal Reset</th>
                        <th>Jenis Reset</th>
                        <th>Santri</th>
                        <th>Total Hari Direset</th>
                        <th>Periode</th>
                        <th>Kuota Tahunan</th>
                        <th>Reset By</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resetLogs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
                            <td>
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; 
                                    {{ $log->jenis_reset == 'massal' ? 'background: #dc3545; color: white;' : 'background: #ffc107; color: #000;' }}">
                                    {{ $log->jenis_reset == 'massal' ? '👥 Massal' : '👤 Individual' }}
                                </span>
                            </td>
                            <td>
                                @if($log->id_santri)
                                    <strong>{{ $log->nama_lengkap ?? $log->id_santri }}</strong><br>
                                    <small style="color: #7F8C8D;">{{ $log->id_santri }}</small>
                                @else
                                    <span style="color: #7F8C8D;">Semua Santri</span>
                                @endif
                            </td>
                            <td>
                                <span style="display: inline-block; background: #6c757d; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                    {{ $log->total_hari_sebelum_reset }} hari
                                </span>
                            </td>
                            <td>
                                <small>
                                    {{ \Carbon\Carbon::parse($log->periode_mulai)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($log->periode_akhir)->format('d M Y') }}
                                </small>
                            </td>
                            <td>{{ $log->kuota_tahunan }} hari</td>
                            <td>{{ $log->reset_by }}</td>
                            <td>
                                <span style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;" 
                                      title="{{ $log->catatan }}">
                                    {{ $log->catatan ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #7F8C8D;">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
            <p>Belum ada history reset kuota</p>
        </div>
    @endif
</div>

{{-- Modal Konfirmasi Reset Semua --}}
<div class="modal fade" id="confirmResetSemuaModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0; color: #dc3545;">
                    <i class="fas fa-exclamation-triangle"></i> KONFIRMASI RESET MASSAL
                </h3>
            </div>
            <div style="padding: 20px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; margin-bottom: 15px;">
                <h4 style="margin: 0 0 10px 0; color: #856404;">⚠️ PERINGATAN PENTING!</h4>
                <p style="margin: 0; color: #856404;">
                    Anda akan mereset kuota untuk <strong>{{ $totalSantri }} santri aktif</strong>. 
                    Semua izin yang berstatus "Disetujui" akan diubah menjadi "Selesai".
                </p>
            </div>
            <div style="background: #e3f2fd; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <p style="margin: 0; font-size: 0.9rem;">
                    <strong>Yang akan terjadi:</strong>
                </p>
                <ul style="margin: 10px 0 0 20px; padding: 0; font-size: 0.9rem;">
                    <li>Semua perhitungan kuota akan direset ke 0</li>
                    <li>Status izin "Disetujui" → "Selesai"</li>
                    <li>Data arsip tetap tersimpan</li>
                    <li>Aktivitas tercatat dalam log</li>
                </ul>
            </div>
            <p style="margin: 15px 0; font-weight: 600; color: #2C3E50;">
                Ketik "<span style="color: #dc3545;">RESET SEMUA</span>" untuk melanjutkan:
            </p>
            <input type="text" 
                   id="confirmationText" 
                   class="form-control" 
                   placeholder="Ketik: RESET SEMUA"
                   style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('confirmResetSemuaModal')">Batal</button>
                <button type="button" class="btn btn-danger" id="executeResetSemuaBtn" disabled>
                    <i class="fas fa-sync-alt"></i> Ya, Reset Semua Kuota
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 600px; width: 90%; margin: auto; }
.modal-content { max-height: 90vh; overflow-y: auto; }

@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Validate confirmation text
document.getElementById('confirmationText')?.addEventListener('input', function() {
    const btn = document.getElementById('executeResetSemuaBtn');
    if (this.value.trim().toUpperCase() === 'RESET SEMUA') {
        btn.disabled = false;
        btn.style.opacity = '1';
    } else {
        btn.disabled = true;
        btn.style.opacity = '0.5';
    }
});

// Show confirmation modal
function confirmResetSemua() {
    const checkbox = document.getElementById('konfirmasi_reset_semua');
    if (!checkbox.checked) {
        alert('Anda harus mencentang konfirmasi terlebih dahulu!');
        return;
    }
    
    document.getElementById('confirmResetSemuaModal').style.display = 'flex';
}

// Execute reset semua
document.getElementById('executeResetSemuaBtn')?.addEventListener('click', function() {
    const btn = this;
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses Reset...';
    
    const formData = new FormData(document.getElementById('resetSemuaForm'));
    
    fetch('{{ route("admin.kepulangan.reset.semua") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('confirmResetSemuaModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showAlert('danger', data.message || 'Terjadi kesalahan saat reset');
        }
    })
    .catch(error => {
        showAlert('danger', 'Error: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// Helper functions
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    // Reset confirmation text
    const confirmInput = document.getElementById('confirmationText');
    if (confirmInput) confirmInput.value = '';
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i> ${message}`;
    
    const pageHeader = document.querySelector('.page-header');
    pageHeader.insertAdjacentElement('afterend', alertDiv);
    
    setTimeout(() => alertDiv.remove(), 5000);
}

// Close modals on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.fade').forEach(modal => {
            modal.style.display = 'none';
        });
    }
});

// Close modal on outside click
document.querySelectorAll('.modal.fade').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

// Auto-set periode akhir when periode mulai changes
document.getElementById('periode_mulai')?.addEventListener('change', function() {
    const mulai = new Date(this.value);
    const akhir = new Date(mulai);
    akhir.setFullYear(akhir.getFullYear() + 1);
    akhir.setDate(akhir.getDate() - 1);
    
    document.getElementById('periode_akhir').value = akhir.toISOString().split('T')[0];
});
</script>
@endsection