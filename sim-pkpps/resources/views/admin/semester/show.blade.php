@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Detail Semester</h2>
</div>

<div class="content-box">
    {{-- Header Section --}}
    <div class="detail-header">
        <div>
            <h3>{{ $semester->nama_semester }}</h3>
            <p class="text-muted">{{ $semester->id_semester }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.semester.edit', $semester) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.semester.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row-cards" style="margin: 22px 0;">
        <div class="card card-info">
            <h3>Total Capaian</h3>
            <div class="card-value">{{ $totalCapaian }}</div>
            <p class="text-muted">Data capaian tercatat</p>
            <i class="fas fa-clipboard-list card-icon"></i>
        </div>
        <div class="card card-success">
            <h3>Santri Aktif</h3>
            <div class="card-value">{{ $santriUnik }}</div>
            <p class="text-muted">Santri dengan capaian</p>
            <i class="fas fa-users card-icon"></i>
        </div>
        <div class="card card-primary">
            <h3>Rata-rata Progress</h3>
            <div class="card-value">{{ number_format($rataRataPersentase, 1) }}%</div>
            <p class="text-muted">Progress keseluruhan</p>
            <i class="fas fa-chart-line card-icon"></i>
        </div>
    </div>

    {{-- Detail Section --}}
    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Semester</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-fingerprint"></i> ID Semester</th>
                <td><strong>{{ $semester->id_semester }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-graduation-cap"></i> Nama Semester</th>
                <td><strong>{{ $semester->nama_semester }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-alt"></i> Tahun Ajaran</th>
                <td>{{ $semester->tahun_ajaran }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-list-ol"></i> Periode</th>
                <td>
                    <span class="badge {{ $semester->periode == 1 ? 'badge-info' : 'badge-warning' }}">
                        Semester {{ $semester->periode }}
                    </span>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-check"></i> Tanggal Mulai</th>
                <td>{{ $semester->tanggal_mulai->format('d F Y') }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-times"></i> Tanggal Akhir</th>
                <td>{{ $semester->tanggal_akhir->format('d F Y') }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-clock"></i> Durasi</th>
                <td>{{ $semester->tanggal_mulai->diffInDays($semester->tanggal_akhir) }} hari</td>
            </tr>
            <tr>
                <th><i class="fas fa-toggle-on"></i> Status</th>
                <td>{!! $semester->status_badge !!}</td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-plus"></i> Dibuat Pada</th>
                <td>{{ $semester->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>
</div>
@endsection