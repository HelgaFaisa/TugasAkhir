{{-- resources/views/admin/pembayaran-spp/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Pembayaran SPP')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-invoice-dollar"></i> Detail Pembayaran SPP</h2>
</div>

<div class="content-box">
    <!-- Header dengan Action Buttons -->
    <div class="detail-header">
        <h3>{{ $pembayaranSpp->id_pembayaran }}</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.pembayaran-spp.cetak-bukti', $pembayaranSpp->id) }}" 
               class="btn btn-success btn-sm hover-shadow"
               target="_blank">
                <i class="fas fa-print"></i> Cetak Bukti
            </a>
            <a href="{{ route('admin.pembayaran-spp.edit', $pembayaranSpp->id) }}" 
               class="btn btn-warning btn-sm hover-shadow">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.pembayaran-spp.index') }}" 
               class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Pembayaran -->
    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Pembayaran</h4>
        <table class="detail-table">
            <tr>
                <th>ID Pembayaran</th>
                <td><strong>{{ $pembayaranSpp->id_pembayaran }}</strong></td>
            </tr>
            <tr>
                <th>Periode</th>
                <td><strong>{{ $pembayaranSpp->periode_lengkap }}</strong></td>
            </tr>
            <tr>
                <th>Nominal</th>
                <td><strong style="color: var(--primary-color); font-size: 1.2rem;">{{ $pembayaranSpp->nominal_format }}</strong></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{!! $pembayaranSpp->status_badge !!}</td>
            </tr>
            <tr>
                <th>Batas Bayar</th>
                <td>
                    {{ $pembayaranSpp->batas_bayar->format('d F Y') }}
                    @if($pembayaranSpp->isTelat())
                        <br>
                        <span class="badge badge-danger" style="margin-top: 5px;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Terlambat {{ $pembayaranSpp->batas_bayar->diffInDays(now()) }} hari
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Tanggal Bayar</th>
                <td>
                    @if($pembayaranSpp->tanggal_bayar)
                        {{ $pembayaranSpp->tanggal_bayar->format('d F Y') }}
                    @else
                        <span class="text-muted">Belum dibayar</span>
                    @endif
                </td>
            </tr>
            @if($pembayaranSpp->keterangan)
                <tr>
                    <th>Keterangan</th>
                    <td>{{ $pembayaranSpp->keterangan }}</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- Informasi Santri -->
    <div class="detail-section">
        <h4><i class="fas fa-user-graduate"></i> Informasi Santri</h4>
        <table class="detail-table">
            <tr>
                <th>ID Santri</th>
                <td><strong>{{ $pembayaranSpp->santri->id_santri }}</strong></td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td><strong>{{ $pembayaranSpp->santri->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <th>NIS</th>
                <td>{{ $pembayaranSpp->santri->nis ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td>{{ $pembayaranSpp->santri->kelas_lengkap }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{!! $pembayaranSpp->santri->status_badge !!}</td>
            </tr>
            <tr>
                <th>Aksi</th>
                <td>
                    <a href="{{ route('admin.pembayaran-spp.riwayat', $pembayaranSpp->santri->id_santri) }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-history"></i> Lihat Riwayat Pembayaran
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <!-- Metadata -->
    <div class="detail-section">
        <h4><i class="fas fa-clock"></i> Metadata</h4>
        <table class="detail-table">
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $pembayaranSpp->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <th>Diperbarui Pada</th>
                <td>{{ $pembayaranSpp->updated_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>
</div>
@endsection