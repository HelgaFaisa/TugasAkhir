@extends('layouts.app')

@section('title', 'Riwayat Kepulangan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-home"></i> Riwayat Kepulangan</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Riwayat izin pulang <strong>{{ $santri->nama_lengkap }}</strong>
    </p>
</div>

{{-- Statistik Cards --}}
<div class="row-cards">
    <div class="card card-info">
        <h3><i class="fas fa-clipboard-list"></i> Total Pengajuan</h3>
        <div class="card-value">{{ $statistik['total_izin'] }}</div>
        <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Tahun {{ $tahunSekarang }}
        </p>
    </div>
    
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Disetujui</h3>
        <div class="card-value">{{ $statistik['disetujui'] }}</div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Izin diterima
        </p>
    </div>
    
    <div class="card card-{{ $statistik['over_limit'] ? 'danger' : 'primary' }}">
        <h3><i class="fas fa-calendar-alt"></i> Total Hari Pulang</h3>
        <div class="card-value">{{ $statistik['total_hari'] }} Hari</div>
        <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
        @if($statistik['over_limit'])
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--danger-color);">
                <i class="fas fa-exclamation-triangle"></i> Melebihi batas!
            </p>
        @else
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
                Dari kuota 12 hari
            </p>
        @endif
    </div>
    
    <div class="card card-{{ $statistik['sisa_kuota'] > 0 ? 'warning' : 'danger' }}">
        <h3><i class="fas fa-hourglass-half"></i> Sisa Kuota</h3>
        <div class="card-value">{{ $statistik['sisa_kuota'] }} Hari</div>
        <div class="card-icon"><i class="fas fa-hourglass-half"></i></div>
        @if($statistik['menunggu'] > 0)
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--warning-color);">
                <i class="fas fa-clock"></i> {{ $statistik['menunggu'] }} menunggu
            </p>
        @else
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
                Kuota tersisa
            </p>
        @endif
    </div>
</div>

{{-- Info Box Kuota --}}
@if($statistik['over_limit'])
<div class="alert alert-danger" style="margin-top: 20px;">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Peringatan:</strong> Anda telah melebihi batas kuota kepulangan (12 hari/tahun). 
    Total hari pulang Anda tahun ini: <strong>{{ $statistik['total_hari'] }} hari</strong>.
</div>
@elseif($statistik['sisa_kuota'] <= 3 && $statistik['sisa_kuota'] > 0)
<div class="alert alert-warning" style="margin-top: 20px;">
    <i class="fas fa-info-circle"></i>
    <strong>Perhatian:</strong> Sisa kuota kepulangan Anda tinggal <strong>{{ $statistik['sisa_kuota'] }} hari</strong>. 
    Gunakan dengan bijak.
</div>
@endif

{{-- Filter --}}
<div class="content-box" style="margin-top: 20px;">
    <form method="GET" class="filter-form-inline">
        <select name="tahun" class="form-control">
            @foreach($tahunOptions as $year)
                <option value="{{ $year }}" {{ $tahunSekarang == $year ? 'selected' : '' }}>
                    Tahun {{ $year }}
                </option>
            @endforeach
        </select>
        
        <select name="status" class="form-control">
            <option value="">Semua Status</option>
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <a href="{{ route('santri.kepulangan.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-sync"></i> Reset
        </a>
    </form>
</div>

{{-- Riwayat Kepulangan --}}
@if($riwayatKepulangan->isEmpty())
    <div class="empty-state" style="margin-top: 20px;">
        <i class="fas fa-home"></i>
        <h3>Belum Ada Riwayat Kepulangan</h3>
        <p>Anda belum pernah mengajukan izin kepulangan pada periode yang dipilih.</p>
    </div>
@else
    <div class="content-box" style="margin-top: 20px;">
        <h3 style="margin: 0 0 15px 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Daftar Riwayat ({{ $riwayatKepulangan->total() }} data)
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @foreach($riwayatKepulangan as $item)
            <a href="{{ route('santri.kepulangan.show', $item->id_kepulangan) }}" 
               style="display: flex; gap: 15px; padding: 15px; background: linear-gradient(135deg, #FFFFFF 0%, #FEFFFE 100%); border-radius: var(--border-radius-sm); border-left: 4px solid 
               @if($item->status == 'Menunggu') var(--warning-color)
               @elseif($item->status == 'Disetujui') var(--success-color)
               @elseif($item->status == 'Ditolak') var(--danger-color)
               @else var(--text-light) @endif
               ; text-decoration: none; transition: var(--transition-base); position: relative;"
               onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateX(5px)';"
               onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
                
                {{-- Icon Status --}}
                <div style="flex-shrink: 0; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: 
                    @if($item->status == 'Menunggu') linear-gradient(135deg, #FFF8E1, #FFF3CD)
                    @elseif($item->status == 'Disetujui') linear-gradient(135deg, #E8F7F2, #D4F1E3)
                    @elseif($item->status == 'Ditolak') linear-gradient(135deg, #FFE8EA, #FFD5D8)
                    @else linear-gradient(135deg, #E2E3E5, #D6D8DB) @endif
                    ;">
                    <i class="fas 
                        @if($item->status == 'Menunggu') fa-clock
                        @elseif($item->status == 'Disetujui') fa-check-circle
                        @elseif($item->status == 'Ditolak') fa-times-circle
                        @else fa-flag-checkered @endif
                        " style="font-size: 1.8rem; color: 
                        @if($item->status == 'Menunggu') var(--warning-color)
                        @elseif($item->status == 'Disetujui') var(--success-color)
                        @elseif($item->status == 'Ditolak') var(--danger-color)
                        @else var(--text-light) @endif
                        ;"></i>
                </div>
                
                {{-- Konten --}}
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">
                                {{ $item->alasan }}
                            </h3>
                            <span class="badge badge-{{ 
                                $item->status == 'Menunggu' ? 'warning' : 
                                ($item->status == 'Disetujui' ? 'success' : 
                                ($item->status == 'Ditolak' ? 'danger' : 'secondary')) 
                            }}">
                                {{ $item->status }}
                            </span>
                        </div>
                        
                        <p style="margin: 0 0 8px 0; font-size: 0.9rem; color: var(--text-light);">
                            <i class="fas fa-code"></i> {{ $item->id_kepulangan }}
                        </p>
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 15px; font-size: 0.85rem; color: var(--text-light);">
                        <span>
                            <i class="fas fa-calendar-alt"></i> {{ $item->tanggal_pulang_formatted }} - {{ $item->tanggal_kembali_formatted }}
                        </span>
                        <span class="badge badge-info badge-sm">
                            <i class="fas fa-clock"></i> {{ $item->durasi_izin }} hari
                        </span>
                        @if($item->status == 'Disetujui')
                            @if($item->is_aktif)
                                <span class="badge badge-success badge-sm">
                                    <i class="fas fa-home"></i> Sedang Pulang
                                </span>
                            @elseif($item->is_terlambat)
                                <span class="badge badge-danger badge-sm">
                                    <i class="fas fa-exclamation-triangle"></i> Terlambat Kembali
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
                
                {{-- Arrow --}}
                <div style="flex-shrink: 0; display: flex; align-items: center;">
                    <i class="fas fa-chevron-right" style="color: var(--text-light);"></i>
                </div>
            </a>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div style="margin-top: 25px;">
            {{ $riwayatKepulangan->links() }}
        </div>
    </div>
@endif

{{-- Info Box --}}
<div class="info-box" style="margin-top: 20px;">
    <i class="fas fa-info-circle"></i>
    <strong>Info:</strong> Kuota kepulangan maksimal <strong>12 hari per tahun</strong>. 
    Pastikan Anda merencanakan kepulangan dengan bijak agar tidak melebihi batas kuota.
</div>

{{-- Quick Actions --}}
<div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('santri.dashboard') }}" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>
@endsection