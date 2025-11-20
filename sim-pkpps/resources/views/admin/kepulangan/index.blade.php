{{-- resources/views/admin/kepulangan/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Data Kepulangan Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-home"></i> Data Kepulangan Santri</h2>
</div>

{{-- Dashboard Cards --}}
<div class="row-cards">
    <div class="card card-info">
        <h3>Total Data</h3>
        <div class="card-value">{{ $stats['total_data'] }}</div>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Menunggu Approval</h3>
        <div class="card-value">{{ $stats['menunggu_approval'] }}</div>
        <i class="fas fa-clock card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Sedang Izin</h3>
        <div class="card-value">{{ $stats['sedang_izin'] }}</div>
        <i class="fas fa-home card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Over Limit</h3>
        <div class="card-value">{{ $stats['over_limit_santri'] }}</div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
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
    {{-- Header Actions --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.kepulangan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Izin Kepulangan
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <form method="GET" action="{{ route('admin.kepulangan.index') }}" id="filterForm" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama, ID, atau alasan..." 
                       value="{{ request('search') }}"
                       id="searchInput">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="status" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="tahun" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="bulan" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.kepulangan.index') }}" class="btn btn-secondary">
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
                    <th>ID</th>
                    <th>Santri</th>
                    <th>Tanggal Pulang</th>
                    <th>Tanggal Kembali</th>
                    <th>Durasi</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kepulangan as $item)
                    <tr style="{{ isset($santriOverLimit[$item->id_santri]) ? 'background-color: #fff3cd;' : '' }}">
                        <td>
                            <strong>{{ $item->id_kepulangan }}</strong>
                            @if(isset($santriOverLimit[$item->id_santri]))
                                <span style="display: inline-block; background: #dc3545; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px;" 
                                      title="Over Limit: {{ $santriOverLimit[$item->id_santri] }} hari">
                                    <i class="fas fa-exclamation-triangle"></i>
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
                        <td>{{ $item->tanggal_pulang_formatted }}</td>
                        <td>{{ $item->tanggal_kembali_formatted }}</td>
                        <td>
                            <span style="display: inline-block; background: {{ $item->durasi_izin_calculated > 7 ? '#ffc107' : '#6c757d' }}; color: {{ $item->durasi_izin_calculated > 7 ? '#000' : '#fff' }}; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                {{ $item->durasi_izin_calculated }} hari
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
                                @elseif($item->status == 'Ditolak') background: #dc3545; color: white;
                                @else background: #6c757d; color: white;
                                @endif">
                                {{ $item->status }}
                            </span>
                            @if($item->is_aktif)
                                <br><small style="color: #28a745; font-weight: 600;">Sedang Izin</small>
                            @elseif($item->is_terlambat)
                                <br><small style="color: #dc3545; font-weight: 600;">Terlambat</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 5px; justify-content: center; flex-wrap: wrap;">
                                <a href="{{ route('admin.kepulangan.show', $item->id_kepulangan) }}" 
                                   class="btn btn-sm btn-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($item->status == 'Menunggu')
                                    <a href="{{ route('admin.kepulangan.edit', $item->id_kepulangan) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-success" 
                                            onclick="approveKepulangan('{{ $item->id_kepulangan }}')"
                                            title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="rejectKepulangan('{{ $item->id_kepulangan }}')"
                                            title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                
                                @if($item->status == 'Disetujui')
                                    <a href="{{ route('admin.kepulangan.print', $item->id_kepulangan) }}" 
                                       class="btn btn-sm btn-secondary" 
                                       target="_blank" title="Cetak Surat">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-success" 
                                            onclick="completeKepulangan('{{ $item->id_kepulangan }}')"
                                            title="Selesaikan">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @endif
                                
                                @if(in_array($item->status, ['Menunggu', 'Ditolak']))
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="deleteKepulangan('{{ $item->id_kepulangan }}')"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                            <p style="color: #7F8C8D;">Tidak ada data kepulangan ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($kepulangan->hasPages())
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 15px;">
            <div>
                Menampilkan {{ $kepulangan->firstItem() ?? 0 }} - {{ $kepulangan->lastItem() ?? 0 }} 
                dari {{ $kepulangan->total() }} data
            </div>
            <div>
                {{ $kepulangan->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

{{-- Modal Approve --}}
<div class="modal fade" id="approveModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <form id="approveForm">
                @csrf
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #2C3E50;">Setujui Izin Kepulangan</h3>
                </div>
                <div class="form-group">
                    <label>Catatan (Opsional):</label>
                    <textarea name="catatan" class="form-control" rows="3" 
                              placeholder="Tambahkan catatan untuk persetujuan ini..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal')">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Reject --}}
<div class="modal fade" id="rejectModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <form id="rejectForm">
                @csrf
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #2C3E50;">Tolak Izin Kepulangan</h3>
                </div>
                <div class="form-group">
                    <label>Alasan Penolakan: <span style="color: #dc3545;">*</span></label>
                    <textarea name="alasan_penolakan" class="form-control" rows="3" 
                              placeholder="Jelaskan alasan penolakan..." required></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Delete --}}
<div class="modal fade" id="deleteModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0; color: #2C3E50;">Konfirmasi Hapus</h3>
            </div>
            <p>Apakah Anda yakin ingin menghapus data kepulangan ini?</p>
            <p style="color: #dc3545; font-size: 0.9rem;">Data yang sudah dihapus tidak dapat dikembalikan.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Hapus
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
let currentActionId = null;

// Auto submit search with debounce
let searchTimeout;
document.getElementById('searchInput')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
});

// Approve
function approveKepulangan(id) {
    currentActionId = id;
    document.getElementById('approveModal').style.display = 'flex';
}

document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/${currentActionId}/approve`, {
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
function rejectKepulangan(id) {
    currentActionId = id;
    document.getElementById('rejectModal').style.display = 'flex';
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/${currentActionId}/reject`, {
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

// Complete
function completeKepulangan(id) {
    if (confirm('Apakah Anda yakin ingin menandai kepulangan ini sebagai selesai?')) {
        fetch(`/admin/kepulangan/${id}/complete`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => showAlert('danger', 'Error: ' + error.message));
    }
}

// Delete
function deleteKepulangan(id) {
    currentActionId = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const btn = this;
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    
    fetch(`/admin/kepulangan/${currentActionId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('deleteModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => showAlert('danger', 'Error: ' + error.message))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
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