@extends('layouts.app')

@section('title', 'Detail Riwayat Kesehatan')

@section('content')
<div class="content-box">
    <div class="detail-header">
        <div>
            <h3>Detail Riwayat Kesehatan</h3>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                ID: <strong>{{ $kesehatanSantri->id_kesehatan }}</strong>
            </p>
        </div>
        <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-secondary btn-sm hover-lift">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <hr style="border: none; border-top: 2px solid var(--primary-light); margin: 20px 0;">
    
    {{-- Status Badge --}}
    <div style="text-align: center; margin: 20px 0;">
        <span class="badge badge-{{ $kesehatanSantri->status_badge_color }} badge-lg">
            <i class="fas 
                @if($kesehatanSantri->status == 'dirawat') fa-procedures
                @elseif($kesehatanSantri->status == 'sembuh') fa-check-circle
                @else fa-home @endif
                "></i>
            Status: {{ ucfirst($kesehatanSantri->status) }}
        </span>
    </div>
    
    {{-- Detail Table --}}
    <table class="detail-table">
        <tr>
            <th><i class="fas fa-code"></i> ID Kesehatan</th>
            <td>{{ $kesehatanSantri->id_kesehatan }}</td>
        </tr>
        <tr>
            <th><i class="fas fa-calendar-plus"></i> Tanggal Masuk UKP</th>
            <td>{{ $kesehatanSantri->tanggal_masuk_formatted }}</td>
        </tr>
        <tr>
            <th><i class="fas fa-calendar-check"></i> Tanggal Keluar UKP</th>
            <td>
                @if($kesehatanSantri->tanggal_keluar)
                    {{ $kesehatanSantri->tanggal_keluar_formatted }}
                @else
                    <span class="badge badge-danger">Belum keluar</span>
                @endif
            </td>
        </tr>
        <tr>
            <th><i class="fas fa-clock"></i> Lama Dirawat</th>
            <td>
                <span class="badge badge-info">{{ $kesehatanSantri->lama_dirawat }} hari</span>
            </td>
        </tr>
        <tr>
            <th><i class="fas fa-stethoscope"></i> Keluhan</th>
            <td><strong style="color: var(--danger-color);">{{ $kesehatanSantri->keluhan }}</strong></td>
        </tr>
        @if($kesehatanSantri->catatan)
        <tr>
            <th><i class="fas fa-notes-medical"></i> Catatan Medis</th>
            <td style="white-space: pre-wrap;">{{ $kesehatanSantri->catatan }}</td>
        </tr>
        @endif
        <tr>
            <th><i class="fas fa-info-circle"></i> Status</th>
            <td>
                <span class="badge badge-{{ $kesehatanSantri->status_badge_color }}">
                    {{ ucfirst($kesehatanSantri->status) }}
                </span>
            </td>
        </tr>
    </table>
</div>

{{-- Info Box --}}
@if($kesehatanSantri->status == 'dirawat')
<div class="info-box" style="margin-top: 20px; background: linear-gradient(135deg, #FFE8EA 0%, #FFD5D8 100%); border-color: var(--danger-color);">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Perhatian:</strong> Anda masih dalam perawatan UKP. Ikuti instruksi petugas kesehatan dan jaga kesehatan Anda.
</div>
@elseif($kesehatanSantri->status == 'sembuh')
<div class="info-box" style="margin-top: 20px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border-color: var(--success-color);">
    <i class="fas fa-check-circle"></i>
    <strong>Alhamdulillah:</strong> Anda sudah sembuh. Jaga kesehatan dan pola hidup sehat agar tidak sakit lagi.
</div>
@endif

{{-- Quick Actions --}}
<div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-primary hover-lift">
        <i class="fas fa-list"></i> Lihat Semua Riwayat
    </a>
    <a href="{{ route('santri.dashboard') }}" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>
@endsection