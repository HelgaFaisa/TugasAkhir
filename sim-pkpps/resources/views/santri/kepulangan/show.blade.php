@extends('layouts.app')

@section('title', 'Detail Kepulangan')

@section('content')
<div class="content-box">
    <div class="detail-header">
        <div>
            <h3>Detail Izin Kepulangan</h3>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                ID: <strong>{{ $kepulangan->id_kepulangan }}</strong>
            </p>
        </div>
        <a href="{{ route('santri.kepulangan.index') }}" class="btn btn-secondary btn-sm hover-lift">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <hr style="border: none; border-top: 2px solid var(--primary-light); margin: 20px 0;">
    
    {{-- Status Badge --}}
    <div style="text-align: center; margin: 20px 0;">
        <span class="badge badge-{{ 
            $kepulangan->status == 'Menunggu' ? 'warning' : 
            ($kepulangan->status == 'Disetujui' ? 'success' : 
            ($kepulangan->status == 'Ditolak' ? 'danger' : 'secondary')) 
        }} badge-lg">
            <i class="fas 
                @if($kepulangan->status == 'Menunggu') fa-clock
                @elseif($kepulangan->status == 'Disetujui') fa-check-circle
                @elseif($kepulangan->status == 'Ditolak') fa-times-circle
                @else fa-flag-checkered @endif
                "></i>
            Status: {{ $kepulangan->status }}
        </span>
        
        @if($kepulangan->status == 'Disetujui' && $kepulangan->is_aktif)
            <span class="badge badge-info badge-lg" style="margin-left: 10px;">
                <i class="fas fa-home"></i> Sedang Dalam Periode Pulang
            </span>
        @elseif($kepulangan->status == 'Disetujui' && $kepulangan->is_terlambat)
            <span class="badge badge-danger badge-lg" style="margin-left: 10px;">
                <i class="fas fa-exclamation-triangle"></i> Terlambat Kembali
            </span>
        @endif
    </div>
    
    {{-- Detail Table --}}
    <table class="detail-table">
        <tr>
            <th><i class="fas fa-code"></i> ID Kepulangan</th>
            <td>{{ $kepulangan->id_kepulangan }}</td>
        </tr>
        <tr>
            <th><i class="fas fa-calendar-plus"></i> Tanggal Pengajuan</th>
            <td>{{ $kepulangan->tanggal_izin_formatted }}</td>
        </tr>
        <tr>
            <th><i class="fas fa-calendar-alt"></i> Tanggal Pulang</th>
            <td><strong style="color: var(--primary-color);">{{ $kepulangan->tanggal_pulang_formatted }}</strong></td>
        </tr>
        <tr>
            <th><i class="fas fa-calendar-check"></i> Tanggal Kembali</th>
            <td><strong style="color: var(--primary-color);">{{ $kepulangan->tanggal_kembali_formatted }}</strong></td>
        </tr>
        <tr>
            <th><i class="fas fa-clock"></i> Durasi Izin</th>
            <td>
                <span class="badge badge-info">{{ $kepulangan->durasi_izin }} hari</span>
            </td>
        </tr>
        <tr>
            <th><i class="fas fa-comment-alt"></i> Alasan Kepulangan</th>
            <td><strong>{{ $kepulangan->alasan }}</strong></td>
        </tr>
        <tr>
            <th><i class="fas fa-info-circle"></i> Status</th>
            <td>
                <span class="badge badge-{{ 
                    $kepulangan->status == 'Menunggu' ? 'warning' : 
                    ($kepulangan->status == 'Disetujui' ? 'success' : 
                    ($kepulangan->status == 'Ditolak' ? 'danger' : 'secondary')) 
                }}">
                    {{ $kepulangan->status }}
                </span>
            </td>
        </tr>
        @if($kepulangan->approved_by)
        <tr>
            <th><i class="fas fa-user-check"></i> Disetujui Oleh</th>
            <td>{{ $kepulangan->approved_by }}</td>
        </tr>
        <tr>
            <th><i class="fas fa-calendar-check"></i> Tanggal Persetujuan</th>
            <td>{{ $kepulangan->approved_at_formatted }}</td>
        </tr>
        @endif
        @if($kepulangan->catatan)
        <tr>
            <th><i class="fas fa-sticky-note"></i> Catatan Admin</th>
            <td style="white-space: pre-wrap;">{{ $kepulangan->catatan }}</td>
        </tr>
        @endif
    </table>
    
    {{-- Info Kuota --}}
    <div style="margin-top: 25px; padding: 20px; background: linear-gradient(135deg, #E3F2FD, #D1E9F9); border-radius: var(--border-radius-sm); border-left: 4px solid var(--info-color);">
        <h4 style="margin: 0 0 10px 0; color: var(--info-color);">
            <i class="fas fa-chart-bar"></i> Informasi Kuota Tahun {{ date('Y') }}
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light);">Total Hari Pulang</p>
                <p style="margin: 5px 0 0 0; font-size: 1.3rem; font-weight: 700; color: var(--primary-color);">
                    {{ $totalHariTahunIni }} hari
                </p>
            </div>
            <div>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light);">Sisa Kuota</p>
                <p style="margin: 5px 0 0 0; font-size: 1.3rem; font-weight: 700;color: {{ $sisaKuota > 0 ? 'var(--success-color)' : 'var(--danger-color)' }};">
                    {{ $sisaKuota }} hari
                </p>
            </div>
            <div>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light);">Kuota Maksimal</p>
                <p style="margin: 5px 0 0 0; font-size: 1.3rem; font-weight: 700; color: var(--text-color);">
                    12 hari
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Alert berdasarkan Status --}}
@if($kepulangan->status == 'Menunggu')
<div class="info-box" style="margin-top: 20px; background: linear-gradient(135deg, #FFF8E1 0%, #FFF3CD 100%); border-color: var(--warning-color);">
    <i class="fas fa-clock"></i>
    <strong>Menunggu Persetujuan:</strong> Izin kepulangan Anda sedang dalam proses review. 
    Mohon tunggu konfirmasi dari pengurus.
</div>
@elseif($kepulangan->status == 'Disetujui')
    @if($kepulangan->is_aktif)
    <div class="info-box" style="margin-top: 20px; background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); border-color: var(--info-color);">
        <i class="fas fa-home"></i>
        <strong>Sedang Pulang:</strong> Anda sedang dalam periode kepulangan. 
        Pastikan kembali sesuai jadwal: <strong>{{ $kepulangan->tanggal_kembali_formatted }}</strong>.
    </div>
    @elseif($kepulangan->is_terlambat)
    <div class="alert alert-danger" style="margin-top: 20px;">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Terlambat Kembali:</strong> Anda telah melewati tanggal kembali yang dijadwalkan. 
        Segera hubungi pengurus!
    </div>
    @else
    <div class="info-box" style="margin-top: 20px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border-color: var(--success-color);">
        <i class="fas fa-check-circle"></i>
        <strong>Izin Disetujui:</strong> Kepulangan Anda telah disetujui. 
        Pastikan untuk pulang dan kembali sesuai jadwal yang telah ditentukan.
    </div>
    @endif
@elseif($kepulangan->status == 'Ditolak')
<div class="alert alert-danger" style="margin-top: 20px;">
    <i class="fas fa-times-circle"></i>
    <strong>Izin Ditolak:</strong> Maaf, izin kepulangan Anda tidak disetujui. 
    @if($kepulangan->catatan)
        Alasan: <strong>{{ $kepulangan->catatan }}</strong>
    @endif
</div>
@elseif($kepulangan->status == 'Selesai')
<div class="info-box" style="margin-top: 20px; background: linear-gradient(135deg, #E2E3E5 0%, #D6D8DB 100%); border-color: var(--text-light);">
    <i class="fas fa-flag-checkered"></i>
    <strong>Kepulangan Selesai:</strong> Anda telah menyelesaikan periode kepulangan ini.
</div>
@endif

{{-- Quick Actions --}}
<div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('santri.kepulangan.index') }}" class="btn btn-primary hover-lift">
        <i class="fas fa-list"></i> Lihat Semua Riwayat
    </a>
    <a href="{{ route('santri.dashboard') }}" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>
@endsection