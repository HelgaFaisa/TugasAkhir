{{-- resources/views/admin/pembayaran-spp/riwayat.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran SPP')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Pembayaran SPP</h2>
</div>

<!-- Info Santri -->
<div class="content-box" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 11px;">
        <div>
            <h3 style="margin: 0; color: var(--primary-color);">{{ $santri->nama_lengkap }}</h3>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                {{ $santri->id_santri }} - {{ $santri->nis ?? '-' }}  {{ $santri->kelas_lengkap }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.santri.show', $santri->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user"></i> Profil Santri
            </a>
            <a href="{{ route('admin.pembayaran-spp.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Statistik -->
<div class="row-cards" style="margin-bottom: 25px;">
    <div class="card card-success">
        <h3>Total Terbayar</h3>
        <div class="card-value">{{ 'Rp ' . number_format($totalBayar, 0, ',', '.') }}</div>
        <i class="fas fa-check-circle card-icon"></i>
    </div>

    <div class="card card-danger">
        <h3>Total Tunggakan</h3>
        <div class="card-value">{{ 'Rp ' . number_format($totalTunggakan, 0, ',', '.') }}</div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
    </div>

    <div class="card card-warning">
        <h3>Pembayaran Telat</h3>
        <div class="card-value">{{ $jumlahTelat }}</div>
        <i class="fas fa-clock card-icon"></i>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="content-box">
    <h4 style="margin-bottom: 14px; color: var(--primary-dark);">
        <i class="fas fa-list"></i> Daftar Pembayaran
    </h4>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembayaran</th>
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Batas Bayar</th>
                    <th>Tanggal Bayar</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaranSpp as $index => $spp)
                    <tr>
                        <td>{{ $pembayaranSpp->firstItem() + $index }}</td>
                        <td><strong>{{ $spp->id_pembayaran }}</strong></td>
                        <td>{{ $spp->periode_lengkap }}</td>
                        <td><strong>{{ $spp->nominal_format }}</strong></td>
                        <td>
                            {{ $spp->batas_bayar->format('d/m/Y') }}
                            @if($spp->isTelat())
                                <br><small style="color: #FF8B94;">
                                    <i class="fas fa-exclamation-triangle"></i> Telat
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($spp->tanggal_bayar)
                                {{ $spp->tanggal_bayar->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{!! $spp->status_badge !!}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.pembayaran-spp.show', $spp->id) }}" 
                               class="btn btn-sm btn-primary" 
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pembayaran-spp.edit', $spp->id) }}" 
                               class="btn btn-sm btn-warning" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 22px;">
                            <i class="fas fa-inbox" style="font-size: 2.2rem; color: #ccc; display: block; margin-bottom: 15px;"></i>
                            <p style="color: #999;">Belum ada riwayat pembayaran untuk santri ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pembayaranSpp->hasPages())
        <div style="margin-top: 14px;">
            {{ $pembayaranSpp->links() }}
        </div>
    @endif
</div>
@endsection