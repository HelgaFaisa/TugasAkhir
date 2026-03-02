@extends('layouts.app')

@section('title', 'Detail Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-eye"></i> Detail Pelanggaran</h2>
</div>

<div class="content-box">
    <div style="margin-bottom: 22px;">
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">Informasi Pelanggaran</h3>
        
        <table style="width: 100%; margin-bottom: 14px;">
            <tr>
                <td style="width: 200px; padding: 10px 0; font-weight: 600;">ID Pelanggaran</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-primary" style="font-size: 1em;">{{ $kategori->id_kategori }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Klasifikasi</td>
                <td style="padding: 10px 0;">
                    @if($kategori->klasifikasi)
                        <span class="badge badge-info" style="font-size: 1em;">{{ $kategori->klasifikasi->nama_klasifikasi }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Nama Pelanggaran</td>
                <td style="padding: 10px 0;">{{ $kategori->nama_pelanggaran }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Poin</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-danger" style="font-size: 1.1em; padding: 8px 12px;">
                        <i class="fas fa-star"></i> {{ $kategori->poin }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Kafaroh / Taqorrub</td>
                <td style="padding: 10px 0;">
                    @if($kategori->kafaroh)
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid var(--primary-color);">
                            {{ $kategori->kafaroh }}
                        </div>
                    @else
                        <span style="color: var(--text-light);">-</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Status</td>
                <td style="padding: 10px 0;">
                    @if($kategori->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Nonaktif</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Total Digunakan</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-info">{{ $kategori->riwayatPelanggaran->count() }} kali</span>
                </td>
            </tr>
        </table>
    </div>

    @if($kategori->riwayatPelanggaran->isNotEmpty())
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">
            <i class="fas fa-history"></i> Riwayat Penggunaan (5 Terbaru)
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Santri</th>
                    <th style="width: 100px; text-align: center;">Poin</th>
                    <th style="width: 120px; text-align: center;">Status Kafaroh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategori->riwayatPelanggaran->take(5) as $index => $riwayat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($riwayat->tanggal)->format('d M Y') }}</td>
                        <td>
                            @if($riwayat->santri)
                                <strong>{{ $riwayat->santri->nama_lengkap }}</strong><br>
                                <small style="color: var(--text-light);">{{ $riwayat->santri->id_santri }}</small>
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger">{{ $riwayat->poin }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($riwayat->is_kafaroh_selesai)
                                <span class="badge badge-success">Selesai</span>
                            @else
                                <span class="badge badge-warning">Belum Selesai</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="btn-group" style="margin-top: 22px;">
        <a href="{{ route('admin.kategori-pelanggaran.edit', $kategori) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection