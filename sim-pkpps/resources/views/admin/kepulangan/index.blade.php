{{-- resources/views/admin/kepulangan/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Data Kepulangan Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-home"></i> Data Kepulangan Santri</h2>
</div>

{{-- Banner Notifikasi Pengajuan Pending --}}
@php
    $pendingPengajuan = \App\Models\PengajuanKepulangan::where('status', 'Menunggu')->count();
@endphp

@if($pendingPengajuan > 0)
    <div class="alert alert-warning" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #000;">
        <i class="fas fa-bell" style="font-size: 2rem;"></i>
        <div style="flex: 1;">
            <strong style="font-size: 1.1rem;">Ada {{ $pendingPengajuan }} pengajuan kepulangan dari mobile yang menunggu review!</strong>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Klik tombol di bawah untuk melihat dan meninjau pengajuan.</p>
        </div>
        <a href="{{ route('admin.kepulangan.pengajuan') }}" class="btn btn-dark" style="white-space: nowrap;">
            <i class="fas fa-mobile-alt"></i> Lihat Pengajuan
        </a>
    </div>
@endif

{{-- Info Periode Kuota --}}
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: center;">
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">📅 Periode Kuota</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">
                {{ $settings->periode_mulai->format('d M Y') }} - {{ $settings->periode_akhir->format('d M Y') }}
            </p>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">📊 Kuota Maksimal</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">{{ $settings->kuota_maksimal }} Hari / Tahun</p>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">🔄 Terakhir Reset</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">
                {{ $settings->terakhir_reset ? $settings->terakhir_reset->format('d M Y') : 'Belum Pernah' }}
            </p>
        </div>
        <div style="text-align: right;">
            <a href="{{ route('admin.kepulangan.settings') }}" class="btn btn-light" style="background: white; color: #667eea; font-weight: 600;">
                <i class="fas fa-cog"></i> Kelola Pengaturan
            </a>
        </div>
    </div>
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
        <h3>Over Limit (>{{ $settings->kuota_maksimal }} Hari)</h3>
        <div class="card-value">{{ $stats['over_limit_santri'] }}</div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
        @if($stats['over_limit_santri'] > 0)
            <a href="{{ route('admin.kepulangan.over-limit') }}" style="font-size: 0.85rem; color: #dc3545; text-decoration: underline; margin-top: 5px; display: block;">
                Lihat Detail
            </a>
        @endif
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
            
            {{-- TOMBOL PENGAJUAN MOBILE (BARU) --}}
            <a href="{{ route('admin.kepulangan.pengajuan') }}" class="btn btn-warning">
                <i class="fas fa-mobile-alt"></i> Pengajuan izin
                @if($pendingPengajuan > 0)
                    <span class="badge" style="background: #dc3545; color: white; margin-left: 5px; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem;">
                        {{ $pendingPengajuan }}
                    </span>
                @endif
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
                       placeholder="Cari nama, ID, atau alasan..." 
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

    {{-- Data Table (SAMA SEPERTI SEBELUMNYA) --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Santri</th>
                    <th>Tanggal Pulang</th>
                    <th>Tanggal Kembali</th>
                    <th>Durasi</th>
                    <th>Total Kuota Terpakai</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kepulangan as $item)
                    @php
                        $isOverLimit = isset($santriOverLimit[$item->id_santri]);
                        $totalHariTerpakai = $isOverLimit ? $santriOverLimit[$item->id_santri] : 0;
                    @endphp
                    <tr style="{{ $isOverLimit ? 'background-color: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545;' : '' }}">
                        <td>
                            <strong>{{ $item->id_kepulangan }}</strong>
                            @if($isOverLimit)
                                <span style="display: inline-block; background: #dc3545; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px; animation: pulse 2s infinite;" 
                                      title="Over Limit: {{ $totalHariTerpakai }} hari">
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
                            <span style="display: inline-block; background: {{ $item->durasi_izin > 7 ? '#ffc107' : '#6c757d' }}; color: {{ $item->durasi_izin > 7 ? '#000' : '#fff' }}; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                {{ $item->durasi_izin }} hari
                            </span>
                        </td>
                        <td>
                            @php
                                $kuotaSantri = \App\Models\Kepulangan::getSisaKuotaSantri($item->id_santri);
                                $badgeColor = $kuotaSantri['badge_color'];
                                $badgeColors = [
                                    'success' => '#28a745',
                                    'warning' => '#ffc107',
                                    'danger' => '#dc3545'
                                ];
                                $bgColor = $badgeColors[$badgeColor] ?? '#6c757d';
                                $textColor = $badgeColor == 'warning' ? '#000' : '#fff';
                            @endphp
                            <div style="text-align: center;">
                                <span style="display: inline-block; background: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                    {{ $kuotaSantri['total_terpakai'] }} / {{ $kuotaSantri['kuota_maksimal'] }} hari
                                </span>
                                <div style="margin-top: 5px; font-size: 0.75rem; color: #7F8C8D;">
                                    @if($kuotaSantri['status'] === 'melebihi')
                                        <strong style="color: #dc3545;">OVER LIMIT</strong>
                                    @else
                                        Sisa: {{ $kuotaSantri['sisa_kuota'] }} hari ({{ $kuotaSantri['persentase'] }}%)
                                    @endif
                                </div>
                            </div>
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
                                <br><small style="color: #28a745; font-weight: 600;">🏠 Sedang Izin</small>
                            @elseif($item->is_terlambat)
                                <br><small style="color: #dc3545; font-weight: 600;">⏰ Terlambat</small>
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
                                            onclick="completeKepulangan('{{ $item->id_kepulangan }}', '{{ $item->santri->nama_lengkap }}', '{{ $item->tanggal_pulang->format('Y-m-d') }}', '{{ $item->tanggal_kembali->format('Y-m-d') }}', {{ $item->durasi_izin }})"
                                            title="Selesaikan">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @endif
                                
                                @if(in_array($item->status, ['Menunggu', 'Ditolak', 'Selesai']))
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
                        <td colspan="9" style="text-align: center; padding: 40px;">
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

{{-- Modal Complete (Selesaikan Kepulangan) --}}
<div class="modal fade" id="completeModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <form id="completeForm">
                @csrf
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #2C3E50;">
                        <i class="fas fa-check-circle" style="color: #28a745;"></i> 
                        Selesaikan Kepulangan
                    </h3>
                </div>
                
                <div style="background: #E8F7F2; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #6FBA9D;">
                    <p style="margin: 5px 0;"><strong>ID Kepulangan:</strong> <span id="completeIdKepulangan"></span></p>
                    <p style="margin: 5px 0;"><strong>Santri:</strong> <span id="completeNamaSantri"></span></p>
                    <p style="margin: 5px 0;"><strong>Tanggal Pulang:</strong> <span id="completeTanggalPulang"></span></p>
                    <p style="margin: 5px 0;"><strong>Rencana Kembali:</strong> <span id="completeTanggalKembaliRencana"></span></p>
                    <p style="margin: 5px 0;"><strong>Durasi Rencana:</strong> <span id="completeDurasiRencana"></span> hari</p>
                </div>

                <div class="form-group">
                    <label for="tanggal_kembali_aktual">
                        <i class="fas fa-calendar-check"></i> 
                        Tanggal Kembali Aktual <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_kembali_aktual" 
                           id="tanggal_kembali_aktual" 
                           class="form-control" 
                           required>
                    <small style="color: #7F8C8D; margin-top: 5px; display: block;">
                        Masukkan tanggal santri kembali ke pesantren. Jika pulang lebih cepat, kuota akan disesuaikan otomatis.
                    </small>
                </div>

                <div id="durasiAktualInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #007bff; display: none;">
                    <p style="margin: 0;"><strong>Durasi Aktual:</strong> <span id="durasiAktual" style="font-weight: 600; color: #007bff;">-</span> hari</p>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #7F8C8D;" id="selisihInfo"></p>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('completeModal')">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 500px; width: 90%; margin: auto; }
.modal-content { max-height: 90vh; overflow-y: auto; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<script>
let currentActionId = null;

// Auto submit search dengan debounce
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

// Complete (Selesaikan Kepulangan)
let currentCompleteData = {};

function completeKepulangan(id, namaSantri, tanggalPulang, tanggalKembaliRencana, durasiRencana) {
    currentCompleteData = {
        id: id,
        namaSantri: namaSantri,
        tanggalPulang: tanggalPulang,
        tanggalKembaliRencana: tanggalKembaliRencana,
        durasiRencana: durasiRencana
    };
    
    // Populate modal
    document.getElementById('completeIdKepulangan').textContent = id;
    document.getElementById('completeNamaSantri').textContent = namaSantri;
    document.getElementById('completeTanggalPulang').textContent = formatTanggal(tanggalPulang);
    document.getElementById('completeTanggalKembaliRencana').textContent = formatTanggal(tanggalKembaliRencana);
    document.getElementById('completeDurasiRencana').textContent = durasiRencana;
    
    // Set default tanggal kembali aktual = hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_kembali_aktual').value = today;
    document.getElementById('tanggal_kembali_aktual').min = tanggalPulang;
    
    // Hitung durasi aktual
    calculateDurasiAktual();
    
    // Show modal
    document.getElementById('completeModal').style.display = 'flex';
}

// Calculate durasi aktual
function calculateDurasiAktual() {
    const tanggalPulang = currentCompleteData.tanggalPulang;
    const tanggalKembaliAktual = document.getElementById('tanggal_kembali_aktual').value;
    
    if (!tanggalKembaliAktual) return;
    
    const startDate = new Date(tanggalPulang);
    const endDate = new Date(tanggalKembaliAktual);
    
    if (endDate < startDate) {
        document.getElementById('durasiAktualInfo').style.display = 'none';
        return;
    }
    
    const diffTime = Math.abs(endDate - startDate);
    const durasiAktual = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    const durasiRencana = currentCompleteData.durasiRencana;
    
    document.getElementById('durasiAktual').textContent = durasiAktual;
    document.getElementById('durasiAktualInfo').style.display = 'block';
    
    // Show selisih
    let selisihText = '';
    let selisihColor = '#007bff';
    
    if (durasiAktual < durasiRencana) {
        const selisih = durasiRencana - durasiAktual;
        selisihText = `✅ Santri pulang ${selisih} hari lebih cepat dari rencana. Kuota akan berkurang ${durasiAktual} hari.`;
        selisihColor = '#28a745';
    } else if (durasiAktual > durasiRencana) {
        const selisih = durasiAktual - durasiRencana;
        selisihText = `⚠️ Santri pulang ${selisih} hari lebih lambat dari rencana. Kuota akan bertambah ${selisih} hari.`;
        selisihColor = '#ffc107';
    } else {
        selisihText = `✓ Sesuai rencana (${durasiAktual} hari).`;
        selisihColor = '#007bff';
    }
    
    const selisihInfo = document.getElementById('selisihInfo');
    selisihInfo.textContent = selisihText;
    selisihInfo.style.color = selisihColor;
    document.getElementById('durasiAktual').style.color = selisihColor;
}

// Event listener untuk tanggal kembali aktual
document.getElementById('tanggal_kembali_aktual')?.addEventListener('change', calculateDurasiAktual);

// Submit form complete
document.getElementById('completeForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/${currentCompleteData.id}/complete`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('completeModal');
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

// Helper: Format tanggal
function formatTanggal(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
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