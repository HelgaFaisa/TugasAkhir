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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
            <h3 style="margin: 0;">Informasi Kepulangan</h3>
            <span style="display: inline-block; padding: 8px 16px; border-radius: 6px; font-size: 1rem; font-weight: 600;
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
                <td><strong>{{ $kepulangan->id_kepulangan }}</strong></td>
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
                    <span style="display: inline-block; background: {{ $kepulangan->durasi_izin > 7 ? '#ffc107' : '#007bff' }}; color: {{ $kepulangan->durasi_izin > 7 ? '#000' : 'white' }}; padding: 6px 12px; border-radius: 6px; font-size: 1rem; font-weight: 600;">
                        {{ $kepulangan->durasi_izin }} hari
                    </span>
                </td>
            </tr>
            <tr>
                <th>Status Kepulangan:</th>
                <td>
                    @if($kepulangan->is_aktif)
                        <span style="display: inline-block; background: #28a745; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem;">
                            Ã°Å¸ÂÂ  Sedang Izin
                        </span>
                    @elseif($kepulangan->is_terlambat)
                        <span style="display: inline-block; background: #dc3545; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem;">
                            Ã¢ÂÂ° Terlambat Kembali
                        </span>
                    @elseif($kepulangan->status == 'Selesai')
                        <span style="display: inline-block; background: #6c757d; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem;">
                            Ã¢Å“â€¦ Sudah Selesai
                        </span>
                    @else
                        <span style="display: inline-block; background: #81C6E8; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem;">
                            Ã°Å¸â€œâ€¦ Belum Dimulai
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Alasan:</th>
                <td>{{ $kepulangan->alasan }}</td>
            </tr>
            @if($kepulangan->approved_by)
            <tr>
                <th>Diproses Oleh:</th>
                <td>{{ $kepulangan->approved_by }}</td>
            </tr>
            <tr>
                <th>Tanggal Diproses:</th>
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
        <div style="margin-top: 14px; display: flex; gap: 10px; flex-wrap: wrap;">
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
        <div class="content-box" style="margin-bottom: 14px;">
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

        {{-- Statistik Kuota Santri --}}
        <div class="content-box" style="margin-bottom: 14px;">
            <h4 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
                <i class="fas fa-chart-pie"></i> Kuota Izin Periode Ini
            </h4>
            
            <div style="background: linear-gradient(135deg, {{ $kuotaSantri['badge_color'] == 'danger' ? '#ff5252 0%, #f48fb1 100%' : ($kuotaSantri['badge_color'] == 'warning' ? '#ffd54f 0%, #ffb74d 100%' : '#81c784 0%, #66bb6a 100%') }}); padding: 14px; border-radius: 12px; text-align: center; color: white; margin-bottom: 15px;">
                <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;">Total Terpakai</div>
                <div style="font-size: 2.2rem; font-weight: 700; line-height: 1;">{{ $kuotaSantri['total_terpakai'] }}</div>
                <div style="font-size: 1rem; opacity: 0.9;">dari {{ $kuotaSantri['kuota_maksimal'] }} hari</div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 11px; margin-bottom: 15px;">
                <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Sisa Kuota</div>
                    <div style="font-size: 1.8rem; font-weight: 700; color: {{ $kuotaSantri['badge_color'] == 'danger' ? '#dc3545' : ($kuotaSantri['badge_color'] == 'warning' ? '#ff9800' : '#28a745') }};">
                        {{ $kuotaSantri['sisa_kuota'] }}
                    </div>
                    <div style="font-size: 0.8rem; color: #7F8C8D;">hari</div>
                </div>
                <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Persentase</div>
                    <div style="font-size: 1.8rem; font-weight: 700; color: {{ $kuotaSantri['badge_color'] == 'danger' ? '#dc3545' : ($kuotaSantri['badge_color'] == 'warning' ? '#ff9800' : '#28a745') }};">
                        {{ $kuotaSantri['persentase'] }}%
                    </div>
                    <div style="font-size: 0.8rem; color: #7F8C8D;">terpakai</div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px; display: block;">
                    Penggunaan Kuota ({{ $kuotaSantri['total_terpakai'] }}/{{ $kuotaSantri['kuota_maksimal'] }} hari)
                </label>
                <div style="width: 100%; height: 15px; background: #E0F0EC; border-radius: 8px; overflow: hidden;">
                    <div style="width: {{ min(100, $kuotaSantri['persentase']) }}%; height: 100%; background: {{ $kuotaSantri['badge_color'] == 'danger' ? '#dc3545' : ($kuotaSantri['badge_color'] == 'warning' ? '#ffc107' : '#28a745') }}; transition: width 0.3s ease;"></div>
                </div>
            </div>

            @if($kuotaSantri['status'] == 'melebihi')
                <div style="padding: 12px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 6px; color: #c62828; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>OVER LIMIT!</strong> Santri ini telah melebihi kuota maksimal.
                </div>
            @elseif($kuotaSantri['status'] == 'hampir_habis')
                <div style="padding: 12px; background: #fff3e0; border: 1px solid #ffe0b2; border-radius: 6px; color: #e65100; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Hampir Habis!</strong> Kuota hampir mencapai batas maksimal.
                </div>
            @else
                <div style="padding: 12px; background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 6px; color: #2e7d32; font-size: 0.9rem;">
                    <i class="fas fa-check-circle"></i>
                    <strong>Aman!</strong> Kuota masih dalam batas normal.
                </div>
            @endif

            <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 0.85rem; color: #7F8C8D;">
                <strong>Periode:</strong><br>
                {{ $kuotaSantri['periode_mulai'] }} - {{ $kuotaSantri['periode_akhir'] }}
            </div>

            {{-- Reset Individual Button --}}
            @if($kuotaSantri['total_terpakai'] > 0)
                <button type="button" 
                        class="btn btn-warning" 
                        style="width: 100%; margin-top: 15px;"
                        onclick="resetKuotaSantri('{{ $kepulangan->id_santri }}', '{{ $kepulangan->santri->nama_lengkap }}')">
                    <i class="fas fa-sync-alt"></i> Reset Kuota Santri Ini
                </button>
            @endif
        </div>

        {{-- Riwayat Izin Periode Ini --}}
        @if(count($detailIzin['details']) > 0)
        <div class="content-box">
            <h4 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
                <i class="fas fa-history"></i> Riwayat Izin Periode Ini
            </h4>
            <div style="position: relative; padding-left: 20px;">
                @foreach($detailIzin['details'] as $detail)
                <div style="position: relative; margin-bottom: 14px; padding: 12px; background: {{ $detail['id'] == $kepulangan->id_kepulangan ? 'rgba(255, 193, 7, 0.1)' : '#f8f9fa' }}; border-radius: 6px; border-left: 3px solid {{ $detail['id'] == $kepulangan->id_kepulangan ? '#ffc107' : '#6FBA9D' }};">
                    @if($detail['id'] == $kepulangan->id_kepulangan)
                        <div style="position: absolute; top: -5px; right: -5px;">
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 5px;">
                        <strong style="color: #2C3E50;">{{ $detail['id'] }}</strong>
                        <span style="display: inline-block; background: {{ $detail['status'] == 'Disetujui' ? '#28a745' : '#6c757d' }}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.75rem;">
                            {{ $detail['status'] }}
                        </span>
                    </div>
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">{{ $detail['tanggal'] }}</div>
                    <div style="font-size: 0.9rem; margin-bottom: 5px;">
                        <span style="display: inline-block; background: #007bff; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.8rem; font-weight: 600;">
                            {{ $detail['durasi'] }} hari
                        </span>
                    </div>
                    <div style="font-size: 0.85rem; color: #7F8C8D;">
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
<div class="content-box" style="margin-top: 14px;">
    <h4 style="margin-top: 0; color: #2C3E50; border-bottom: 2px solid #6FBA9D; padding-bottom: 10px;">
        <i class="fas fa-list"></i> Riwayat Kepulangan Lainnya (5 Terakhir)
    </h4>
    <div style="overflow-x: auto;">
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal Pulang</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                <tr>
                    <td><strong>{{ $item->id_kepulangan }}</strong></td>
                    <td>{{ $item->tanggal_pulang_formatted }}</td>
                    <td>
                        <span style="display: inline-block; background: #6c757d; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                            {{ $item->durasi_izin }} hari
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
                    <td>
                        <a href="{{ route('admin.kepulangan.show', $item->id_kepulangan) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endif

{{-- Modals --}}
<div class="modal fade" id="approveModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 14px;">
            <form id="approveForm">
                @csrf
                <div style="margin-bottom: 14px;">
                    <h3 style="margin: 0; color: #2C3E50;">Setujui Izin Kepulangan</h3>
                </div>
                <div class="form-group">
                    <label>Catatan (Opsional):</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal')">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 14px;">
            <form id="rejectForm">
                @csrf
                <div style="margin-bottom: 14px;">
                    <h3 style="margin: 0; color: #2C3E50;">Tolak Izin Kepulangan</h3>
                </div>
                <div class="form-group">
                    <label>Alasan Penolakan: <span style="color: #dc3545;">*</span></label>
                    <textarea name="alasan_penolakan" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="resetKuotaModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 14px;">
            <form id="resetKuotaForm">
                @csrf
                <div style="margin-bottom: 14px;">
                    <h3 style="margin: 0; color: #2C3E50;">Reset Kuota Santri</h3>
                </div>
                <div style="padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; margin-bottom: 15px;">
                    <p style="margin: 0; color: #856404;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Anda akan mereset kuota untuk santri: <strong id="resetSantriName"></strong>
                    </p>
                </div>
                <div class="form-group">
                    <label>Catatan Reset (Opsional):</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Alasan reset kuota..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('resetKuotaModal')">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Reset Kuota</button>
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
let currentResetSantriId = null;

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

function resetKuotaSantri(idSantri, namaSantri) {
    currentResetSantriId = idSantri;
    document.getElementById('resetSantriName').textContent = namaSantri;
    document.getElementById('resetKuotaModal').style.display = 'flex';
}

document.getElementById('resetKuotaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mereset...';
    
    fetch(`/admin/kepulangan/reset/santri/${currentResetSantriId}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('resetKuotaModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
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

document.querySelectorAll('.modal.fade').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>
@endsection