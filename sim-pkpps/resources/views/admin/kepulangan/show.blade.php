{{-- resources/views/admin/kepulangan/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Detail Kepulangan Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> Detail Kepulangan Santri</h2>
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

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    {{-- Main Detail --}}
    <div class="content-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <h3 style="margin: 0;">Informasi Kepulangan</h3>
            <span style="display: inline-block; padding: 6px 14px; border-radius: 6px; font-size: 1rem; font-weight: 600;
                @if($kepulangan->status == 'Menunggu') background: #ffc107; color: #000;
                @elseif($kepulangan->status == 'Disetujui') background: #28a745; color: white;
                @elseif($kepulangan->status == 'Ditolak') background: #dc3545; color: white;
                @else background: #6c757d; color: white;
                @endif">
                {{ $kepulangan->status }}
            </span>
        </div>

        <table class="detail-table">
            <tr>
                <th>ID Kepulangan:</th>
                <td>{{ $kepulangan->id_kepulangan }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan:</th>
                <td>{{ $kepulangan->tanggal_izin_formatted }}</td>
            </tr>
            <tr>
                <th>Tanggal Pulang:</th>
                <td>{{ $kepulangan->tanggal_pulang_formatted }}</td>
            </tr>
            <tr>
                <th>Tanggal Kembali:</th>
                <td>{{ $kepulangan->tanggal_kembali_formatted }}</td>
            </tr>
            <tr>
                <th>Durasi Izin:</th>
                <td>
                    <span style="display: inline-block; background: {{ $kepulangan->durasi_izin_calculated > 7 ? '#ffc107' : '#007bff' }}; color: {{ $kepulangan->durasi_izin_calculated > 7 ? '#000' : 'white' }}; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem;">
                        {{ $kepulangan->durasi_izin_calculated }} hari
                    </span>
                </td>
            </tr>
            <tr>
                <th>Status Kepulangan:</th>
                <td>
                    @if($kepulangan->is_aktif)
                        <span style="display: inline-block; background: #28a745; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">Sedang Izin</span>
                    @elseif($kepulangan->is_terlambat)
                        <span style="display: inline-block; background: #dc3545; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">Terlambat Kembali</span>
                    @elseif($kepulangan->status == 'Selesai')
                        <span style="display: inline-block; background: #6c757d; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">Sudah Selesai</span>
                    @else
                        <span style="display: inline-block; background: #81C6E8; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">Belum Dimulai</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Alasan:</th>
                <td>{{ $kepulangan->alasan }}</td>
            </tr>
            @if($kepulangan->approved_by)
            <tr>
                <th>Disetujui Oleh:</th>
                <td>{{ $kepulangan->approved_by }}</td>
            </tr>
            <tr>
                <th>Tanggal Persetujuan:</th>
                <td>{{ $kepulangan->approved_at_formatted }}</td>
            </tr>
            @endif
            @if($kepulangan->catatan)
            <tr>
                <th>Catatan:</th>
                <td>{{ $kepulangan->catatan }}</td>
            </tr>
            @endif
        </table>

        {{-- Action Buttons --}}
        <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.kepulangan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            @if($kepulangan->status == 'Menunggu')
                <a href="{{ route('admin.kepulangan.edit', $kepulangan->id_kepulangan) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-success" onclick="approveKepulangan()">
                    <i class="fas fa-check"></i> Setujui
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectKepulangan()">
                    <i class="fas fa-times"></i> Tolak
                </button>
            @endif

            @if($kepulangan->status == 'Disetujui')
                <a href="{{ route('admin.kepulangan.print', $kepulangan->id_kepulangan) }}" 
                   class="btn btn-primary" target="_blank">
                    <i class="fas fa-print"></i> Cetak Surat
                </a>
                <button type="button" class="btn btn-success" onclick="completeKepulangan()">
                    <i class="fas fa-check-double"></i> Tandai Selesai
                </button>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div>
        {{-- Info Santri --}}
        <div class="content-box" style="margin-bottom: 20px;">
            <h4 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
                <i class="fas fa-user"></i> Informasi Santri
            </h4>
            <table class="detail-table">
                <tr>
                    <th>ID Santri:</th>
                    <td>{{ $kepulangan->santri->id_santri }}</td>
                </tr>
                <tr>
                    <th>Nama:</th>
                    <td>{{ $kepulangan->santri->nama_lengkap }}</td>
                </tr>
                <tr>
                    <th>NIS:</th>
                    <td>{{ $kepulangan->santri->nis ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kelas:</th>
                    <td>{{ $kepulangan->santri->kelas }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <span style="display: inline-block; background: {{ $kepulangan->santri->status == 'Aktif' ? '#28a745' : '#6c757d' }}; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">
                            {{ $kepulangan->santri->status }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Statistik Penggunaan Izin --}}
        <div class="content-box" style="margin-bottom: 20px;">
            <h4 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
                <i class="fas fa-chart-bar"></i> Statistik Izin {{ $kepulangan->tanggal_pulang->year }}
            </h4>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0; color: #007bff;">{{ $detailIzin['total_hari'] }}</h3>
                        <small style="color: #7F8C8D;">Total Hari</small>
                    </div>
                    <div>
                        <h3 style="margin: 0; color: #81C6E8;">{{ $detailIzin['total_izin'] }}</h3>
                        <small style="color: #7F8C8D;">Total Izin</small>
                    </div>
                </div>
                <hr style="margin: 15px 0; border: none; border-top: 1px solid #dee2e6;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <h4 style="margin: 0; color: {{ $detailIzin['sisa_kuota'] <= 3 ? '#dc3545' : '#28a745' }};">
                            {{ $detailIzin['sisa_kuota'] }}
                        </h4>
                        <small style="color: #7F8C8D;">Sisa Kuota</small>
                    </div>
                    <div>
                        @if($detailIzin['over_limit'])
                            <span style="display: inline-block; background: #dc3545; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">Over Limit</span>
                        @else
                            <span style="display: inline-block; background: #28a745; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem;">Normal</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div style="margin-top: 15px;">
                <label style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px; display: block;">
                    Penggunaan Kuota ({{ $detailIzin['total_hari'] }}/12 hari)
                </label>
                <div style="width: 100%; height: 10px; background: #E0F0EC; border-radius: 5px; overflow: hidden;">
                    <div style="width: {{ min(100, ($detailIzin['total_hari'] / 12) * 100) }}%; height: 100%; background: {{ $detailIzin['over_limit'] ? '#dc3545' : ($detailIzin['total_hari'] > 8 ? '#ffc107' : '#28a745') }}; transition: width 0.3s ease;"></div>
                </div>
            </div>
        </div>

        {{-- Riwayat Izin --}}
        @if(count($detailIzin['details']) > 0)
        <div class="content-box">
            <h4 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
                <i class="fas fa-history"></i> Riwayat Izin {{ $kepulangan->tanggal_pulang->year }}
            </h4>
            <div style="position: relative; padding-left: 20px;">
                @foreach($detailIzin['details'] as $detail)
                <div style="position: relative; margin-bottom: 20px; padding: 12px; background: {{ $detail['id'] == $kepulangan->id_kepulangan ? 'rgba(255, 193, 7, 0.1)' : '#f8f9fa' }}; border-radius: 6px; border-left: 3px solid {{ $detail['id'] == $kepulangan->id_kepulangan ? '#ffc107' : '#6FBA9D' }};">
                    @if($detail['id'] == $kepulangan->id_kepulangan)
                        <div style="position: absolute; top: -5px; right: -5px;">
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                        </div>
                    @endif
                    <strong style="color: #2C3E50;">{{ $detail['id'] }}</strong>
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-top: 3px;">{{ $detail['tanggal'] }}</div>
                    <div style="font-size: 0.9rem; margin-top: 5px;">
                        <span style="display: inline-block; background: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.8rem;">
                            {{ $detail['durasi'] }} hari
                        </span>
                    </div>
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-top: 5px;">
                        {{ \Illuminate\Support\Str::limit($detail['alasan'], 50) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- History Kepulangan Lainnya --}}
@if($history->count() > 0)
<div class="content-box" style="margin-top: 20px;">
    <h4 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
        <i class="fas fa-list"></i> Riwayat Kepulangan Lainnya
    </h4>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                <tr>
                    <td>
                        <a href="{{ route('admin.kepulangan.show', $item->id_kepulangan) }}" style="color: #6FBA9D; text-decoration: none; font-weight: 600;">
                            {{ $item->id_kepulangan }}
                        </a>
                    </td>
                    <td>{{ $item->tanggal_pulang_formatted }}</td>
                    <td>
                        <span style="display: inline-block; background: #6c757d; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                            {{ $item->durasi_izin_calculated }} hari
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
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($item->alasan, 30) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Modals (sama seperti di index) --}}
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
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal')">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                    <textarea name="alasan_penolakan" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 500px; width: 90%; margin: auto; }
.modal-content { max-height: 90vh; overflow-y: auto; }
@media (max-width: 768px) {
    div[style*="grid-template-columns: 2fr 1fr"] { grid-template-columns: 1fr !important; }
}
</style>

<script>
function approveKepulangan() {
    document.getElementById('approveModal').style.display = 'flex';
}

document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch('{{ route("admin.kepulangan.approve", $kepulangan->id_kepulangan) }}', {
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

function rejectKepulangan() {
    document.getElementById('rejectModal').style.display = 'flex';
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch('{{ route("admin.kepulangan.reject", $kepulangan->id_kepulangan) }}', {
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

function completeKepulangan() {
    if (confirm('Apakah Anda yakin ingin menandai kepulangan ini sebagai selesai?')) {
        fetch('{{ route("admin.kepulangan.complete", $kepulangan->id_kepulangan) }}', {
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.fade').forEach(modal => modal.style.display = 'none');
    }
});
</script>
@endsection