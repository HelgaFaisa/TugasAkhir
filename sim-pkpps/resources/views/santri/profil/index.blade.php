@extends('layouts.app')

@section('title', 'Profil Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-circle"></i> Profil Saya</h2>
</div>

{{-- Alert Success --}}
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="content-box">
    {{-- Header Profil --}}
    <div class="detail-header">
        <div style="display: flex; align-items: center; gap: 20px;">
            {{-- Avatar Santri --}}
            <div class="santri-avatar-initial santri-avatar-initial-lg">
                {{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}
            </div>
            
            <div>
                <h3 style="margin: 0; font-size: 1.8rem; color: var(--text-color);">
                    {{ $santri->nama_lengkap }}
                </h3>
                <p style="margin: 5px 0 0 0; color: var(--text-light);">
                    <i class="fas fa-id-card"></i> {{ $santri->id_santri }}
                    @if($santri->nis)
                        | <i class="fas fa-barcode"></i> NIS: {{ $santri->nis }}
                    @endif
                </p>
            </div>
        </div>
        
        <div>
            <a href="{{ route('santri.profil.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Profil
            </a>
        </div>
    </div>
    
    <hr style="margin: 25px 0; border: none; border-top: 2px solid var(--primary-light);">
    
    {{-- Data Pribadi --}}
    <div class="detail-section">
        <h4><i class="fas fa-user"></i> Data Pribadi</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-id-badge"></i> ID Santri</th>
                <td>{{ $santri->id_santri }}</td>
            </tr>
            @if($santri->nis)
            <tr>
                <th><i class="fas fa-barcode"></i> NIS</th>
                <td>{{ $santri->nis }}</td>
            </tr>
            @endif
            <tr>
                <th><i class="fas fa-user"></i> Nama Lengkap</th>
                <td><strong>{{ $santri->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-venus-mars"></i> Jenis Kelamin</th>
                <td>{{ $santri->jenis_kelamin }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-school"></i> Kelas</th>
                <td>
                    <span class="badge badge-primary badge-lg">
                        <i class="fas fa-graduation-cap"></i> 
                        {{ $santri->kelas_lengkap }}
                    </span>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-info-circle"></i> Status</th>
                <td>{!! $santri->status_badge !!}</td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-plus"></i> Terdaftar Sejak</th>
                <td>{{ $santri->created_at->format('d F Y') }}</td>
            </tr>
        </table>
    </div>
    
    {{-- Data Kontak & Alamat --}}
    <div class="detail-section">
        <h4><i class="fas fa-address-card"></i> Kontak & Alamat</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-map-marker-alt"></i> Alamat Lengkap</th>
                <td>{{ $santri->alamat_santri ?? '-' }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-map"></i> Daerah Asal</th>
                <td>{{ $santri->daerah_asal ?? '-' }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-user-tie"></i> Nama Orang Tua/Wali</th>
                <td>{{ $santri->nama_orang_tua ?? '-' }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-phone"></i> Nomor HP Orang Tua</th>
                <td>
                    @if($santri->nomor_hp_ortu)
                        <a href="tel:{{ $santri->nomor_hp_ortu }}" class="link-primary">
                            <i class="fas fa-phone-alt"></i> {{ $santri->nomor_hp_ortu }}
                        </a>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>
    
    {{-- Data Kartu RFID --}}
    @if($santri->has_rfid)
    <div class="detail-section">
        <h4><i class="fas fa-id-card"></i> Kartu RFID</h4>
        <div class="info-box">
            <p>
                <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                <strong>Kartu RFID Anda sudah terdaftar</strong>
            </p>
            <p style="margin-top: 8px; font-size: 0.9rem;">
                UID: <code style="background: white; padding: 4px 8px; border-radius: 4px; font-weight: 600;">{{ $santri->rfid_uid }}</code>
            </p>
        </div>
    </div>
    @else
    <div class="detail-section">
        <h4><i class="fas fa-id-card"></i> Kartu RFID</h4>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Kartu RFID Anda belum terdaftar. Silakan hubungi admin untuk pendaftaran kartu.
        </div>
    </div>
    @endif
    
    {{-- Info Tambahan --}}
    <div style="margin-top: 30px; padding: 20px; background: var(--primary-light); border-radius: var(--border-radius-sm); border-left: 4px solid var(--primary-color);">
        <p style="margin: 0; color: var(--text-color); line-height: 1.6;">
            <i class="fas fa-info-circle" style="color: var(--primary-color);"></i>
            <strong>Catatan:</strong> Jika ada data yang perlu diperbarui selain alamat dan nomor HP orang tua, silakan hubungi admin atau pengurus pesantren.
        </p>
    </div>
</div>
@endsection