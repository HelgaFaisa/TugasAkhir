{{-- resources/views/santri/pelanggaran/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Riwayat Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Detail Riwayat Pelanggaran</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3>
            <i class="fas fa-exclamation-circle" style="color: var(--danger-color);"></i>
            Riwayat ID: {{ $riwayatPelanggaran->id_riwayat }}
        </h3>
        <div>
            <a href="{{ route('santri.pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Pelanggaran</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-hashtag"></i> ID Riwayat</th>
                <td><strong>{{ $riwayatPelanggaran->id_riwayat }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar"></i> Tanggal Kejadian</th>
                <td>{{ \Carbon\Carbon::parse($riwayatPelanggaran->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-list"></i> Jenis Pelanggaran</th>
                <td><strong>{{ $riwayatPelanggaran->kategori->nama_pelanggaran ?? '-' }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-star"></i> Poin Pelanggaran</th>
                <td>
                    <span class="badge badge-danger badge-lg">
                        <i class="fas fa-star"></i> {{ $riwayatPelanggaran->poin }} Poin
                    </span>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-comment-alt"></i> Keterangan</th>
                <td>{{ $riwayatPelanggaran->keterangan ?: '-' }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-clock"></i> Dicatat Pada</th>
                <td>{{ $riwayatPelanggaran->created_at->isoFormat('D MMMM YYYY, HH:mm') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- Info Box --}}
    <div class="info-box">
        <p>
            <i class="fas fa-info-circle"></i>
            <strong>Catatan:</strong> Data pelanggaran ini dicatat oleh admin/pengurus pondok. 
            Jika ada kesalahan data, silakan hubungi bagian administrasi.
        </p>
    </div>
</div>
@endsection