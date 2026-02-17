@extends('layouts.app')

@section('title', 'Detail Klasifikasi')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-eye"></i> Detail Klasifikasi Pelanggaran</h2>
</div>

<div class="content-box">
    <div style="margin-bottom: 30px;">
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">Informasi Klasifikasi</h3>
        
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 200px; padding: 10px 0; font-weight: 600;">ID Klasifikasi</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-primary" style="font-size: 1em;">{{ $klasifikasi->id_klasifikasi }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Nama Klasifikasi</td>
                <td style="padding: 10px 0;">{{ $klasifikasi->nama_klasifikasi }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Deskripsi</td>
                <td style="padding: 10px 0;">{{ $klasifikasi->deskripsi ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Urutan</td>
                <td style="padding: 10px 0;">{{ $klasifikasi->urutan }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Status</td>
                <td style="padding: 10px 0;">
                    @if($klasifikasi->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Nonaktif</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Jumlah Pelanggaran</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-info">{{ $klasifikasi->pelanggarans->count() }} Pelanggaran</span>
                </td>
            </tr>
        </table>
    </div>

    @if($klasifikasi->pelanggarans->isNotEmpty())
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">
            <i class="fas fa-list"></i> Daftar Pelanggaran
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th>Nama Pelanggaran</th>
                    <th style="width: 80px; text-align: center;">Poin</th>
                    <th style="width: 100px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($klasifikasi->pelanggarans as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge badge-secondary">{{ $item->id_kategori }}</span></td>
                        <td>{{ $item->nama_pelanggaran }}</td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger">{{ $item->poin }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($item->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Belum ada pelanggaran</h3>
            <p>Klasifikasi ini belum memiliki pelanggaran.</p>
        </div>
    @endif

    <div class="btn-group" style="margin-top: 30px;">
        <a href="{{ route('admin.klasifikasi-pelanggaran.edit', $klasifikasi) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.klasifikasi-pelanggaran.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection