{{-- resources/views/admin/kepulangan/pengajuan.blade.php --}}

@extends('layouts.app')

@section('title', 'Pengajuan Kepulangan dari Mobile')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-mobile-alt"></i> Pengajuan Kepulangan dari Mobile</h2>
    <p style="color: #7F8C8D; margin-top: 5px;">Kelola pengajuan izin kepulangan yang diajukan melalui aplikasi mobile</p>
</div>

{{-- Info Banner --}}
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px; border-radius: 12px; margin-bottom: 14px;">
    <div style="display: flex; align-items: center; gap: 11px;">
        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 12px;">
            <i class="fas fa-info-circle" style="font-size: 2rem;"></i>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0;">Tentang Pengajuan dari Mobile</h4>
            <p style="margin: 0; font-size: 0.95rem; opacity: 0.9;">
                Pengajuan ini dikirim oleh wali santri melalui aplikasi mobile. 
                Setelah Anda approve, data akan otomatis masuk ke tabel kepulangan utama dengan status "Disetujui".
            </p>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row-cards">
    <div class="card card-info">
        <h3>Total Pengajuan</h3>
        <div class="card-value">{{ $stats['total_data'] }}</div>
        <i class="fas fa-list card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Menunggu Review</h3>
        <div class="card-value">{{ $stats['menunggu'] }}</div>
        <i class="fas fa-clock card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Disetujui</h3>
        <div class="card-value">{{ $stats['disetujui'] }}</div>
        <i class="fas fa-check-circle card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Ditolak</h3>
        <div class="card-value">{{ $stats['ditolak'] }}</div>
        <i class="fas fa-times-circle card-icon"></i>
    </div>
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

{{-- Main Content --}}
<div class="content-box">
    {{-- Filter Section --}}
    <form method="GET" action="{{ route('admin.kepulangan.pengajuan') }}" id="filterForm" style="margin-bottom: 14px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Cari nama santri atau ID..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="status" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.kepulangan.pengajuan') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Data Table --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Pengajuan</th>
                    <th>Santri</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Pulang - Kembali</th>
                    <th>Durasi</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $item)
                    <tr style="{{ $item->status == 'Menunggu' ? 'background-color: rgba(255, 193, 7, 0.1);' : '' }}">
                        <td>
                            <strong>{{ $item->id_pengajuan }}</strong>
                            @if($item->status == 'Menunggu')
                                <span style="display: inline-block; background: #ffc107; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px;">
                                    <i class="fas fa-clock"></i> BARU
                                </span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <strong>{{ $item->santri->nama_lengkap ?? 'N/A' }}</strong><br>
                                <small style="color: #7F8C8D;">
                                    {{ $item->santri->id_santri ?? '' }} | {{ $item->santri->kelas ?? '' }}
                                </small>
                            </div>
                        </td>
                        <td>
                            {{ $item->created_at->format('d M Y') }}<br>
                            <small style="color: #7F8C8D;">{{ $item->created_at->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            {{ $item->tanggal_pulang->format('d M Y') }}<br>
                            <small style="color: #7F8C8D;">s/d {{ $item->tanggal_kembali->format('d M Y') }}</small>
                        </td>
                        <td>
                            <span style="display: inline-block; background: {{ $item->durasi_izin > 7 ? '#ffc107' : '#6c757d' }}; color: {{ $item->durasi_izin > 7 ? '#000' : '#fff' }}; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                {{ $item->durasi_izin }} hari
                            </span>
                        </td>
                        <td>
                            <span style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;" title="{{ $item->alasan }}">
                                {{ $item->alasan }}
                            </span>
                        </td>
                        <td>
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;
                                @if($item->status == 'Menunggu') background: #ffc107; color: #000;
                                @elseif($item->status == 'Disetujui') background: #28a745; color: white;
                                @else background: #dc3545; color: white;
                                @endif">
                                {{ $item->status }}
                            </span>
                            @if($item->reviewed_at)
                                <br><small style="color: #7F8C8D;">{{ $item->reviewed_at->format('d M Y H:i') }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 5px; justify-content: center; flex-wrap: wrap;">
                                @if($item->status == 'Menunggu')
                                    <button type="button" 
                                            class="btn btn-sm btn-success" 
                                            onclick="approvePengajuan({{ $item->id }})"
                                            title="Setujui">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="rejectPengajuan({{ $item->id }})"
                                            title="Tolak">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                @else
                                    <small style="color: #7F8C8D;">
                                        @if($item->catatan_review)
                                            <strong>Catatan:</strong><br>{{ Str::limit($item->catatan_review, 50) }}
                                        @else
                                            Sudah direview
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 22px;">
                            <i class="fas fa-inbox" style="font-size: 2.2rem; color: #ccc; margin-bottom: 15px;"></i>
                            <p style="color: #7F8C8D;">Tidak ada pengajuan kepulangan dari mobile</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($pengajuan->hasPages())
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px; flex-wrap: wrap; gap: 11px;">
            <div>
                Menampilkan {{ $pengajuan->firstItem() ?? 0 }} - {{ $pengajuan->lastItem() ?? 0 }} 
                dari {{ $pengajuan->total() }} data
            </div>
            <div>
                {{ $pengajuan->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

{{-- Modal Approve --}}
<div class="modal fade" id="approveModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 14px;">
            <form id="approveForm">
                @csrf
                <div style="margin-bottom: 14px;">
                    <h3 style="margin: 0; color: #2C3E50;">
                        <i class="fas fa-check-circle" style="color: #28a745;"></i> 
                        Setujui Pengajuan
                    </h3>
                </div>
                <p>Pengajuan akan otomatis dipindahkan ke tabel kepulangan dengan status <strong>"Disetujui"</strong>.</p>
                <div class="form-group">
                    <label>Catatan Persetujuan (Opsional):</label>
                    <textarea name="catatan_review" class="form-control" rows="3" 
                              placeholder="Tambahkan catatan untuk persetujuan ini..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal')">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Setujui Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Reject --}}
<div class="modal fade" id="rejectModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 14px;">
            <form id="rejectForm">
                @csrf
                <div style="margin-bottom: 14px;">
                    <h3 style="margin: 0; color: #2C3E50;">
                        <i class="fas fa-times-circle" style="color: #dc3545;"></i> 
                        Tolak Pengajuan
                    </h3>
                </div>
                <p style="color: #dc3545;">Pengajuan akan ditolak dan wali santri akan menerima notifikasi penolakan.</p>
                <div class="form-group">
                    <label>Alasan Penolakan: <span style="color: #dc3545;">*</span></label>
                    <textarea name="catatan_review" class="form-control" rows="3" 
                              placeholder="Jelaskan alasan penolakan..." required></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal.fade { 
    position: fixed; 
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 100%; 
    background: rgba(0,0,0,0.5); 
    z-index: 1000; 
    align-items: center; 
    justify-content: center; 
}
.modal-dialog { 
    max-width: 500px; 
    width: 90%; 
    margin: auto; 
}
.modal-content { 
    max-height: 90vh; 
    overflow-y: auto; 
}
</style>

<script>
let currentPengajuanId = null;

// Approve
function approvePengajuan(id) {
    currentPengajuanId = id;
    document.getElementById('approveModal').style.display = 'flex';
}

document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/pengajuan/${currentPengajuanId}/approve`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('approveModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => showAlert('danger', 'Error: ' + error.message))
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Reject
function rejectPengajuan(id) {
    currentPengajuanId = id;
    document.getElementById('rejectModal').style.display = 'flex';
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/pengajuan/${currentPengajuanId}/reject`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('rejectModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => showAlert('danger', 'Error: ' + error.message))
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Helper functions
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
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