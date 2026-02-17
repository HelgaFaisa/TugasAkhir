@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> Detail Kelas</h2>
</div>

<!-- Flash Messages -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Kelas Information -->
<div class="content-box">
    <div class="detail-header">
        <h3>Informasi Kelas: {{ $kela->nama_kelas }}</h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.kelas.edit', $kela->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <table class="detail-table">
        <tr>
            <th>Kode Kelas</th>
            <td><strong>{{ $kela->kode_kelas }}</strong></td>
        </tr>
        <tr>
            <th>Nama Kelas</th>
            <td><strong>{{ $kela->nama_kelas }}</strong></td>
        </tr>
        <tr>
            <th>Kelompok Kelas</th>
            <td>
                <span class="badge badge-info">
                    {{ $kela->kelompok->nama_kelompok }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Urutan</th>
            <td>{{ $kela->urutan }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if ($kela->is_active)
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Aktif
                    </span>
                @else
                    <span class="badge badge-secondary">
                        <i class="fas fa-times-circle"></i> Tidak Aktif
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Jumlah Santri Aktif</th>
            <td>
                <strong>{{ $santriCount }}</strong> santri 
                <span class="text-muted">(Tahun Ajaran: {{ $tahunAjaranAktif }})</span>
            </td>
        </tr>
        <tr>
            <th>Dibuat Pada</th>
            <td>{{ $kela->created_at->format('d M Y H:i') }}</td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $kela->updated_at->format('d M Y H:i') }}</td>
        </tr>
    </table>
</div>

<!-- Santri List in This Kelas -->
@if ($kela->santriKelas->count() > 0)
<div class="content-box" style="margin-top: 20px;">
    <h3 style="margin-bottom: 15px;">
        <i class="fas fa-users"></i> Daftar Santri ({{ $tahunAjaranAktif }})
    </h3>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>ID Santri</th>
                <th>Nama Lengkap</th>
                <th>Status</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $santriList = $kela->santriKelas
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->filter(function($sk) {
                        return $sk->santri && $sk->santri->status === 'Aktif';
                    });
            @endphp
            @foreach ($santriList as $index => $santriKelas)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $santriKelas->santri->id_santri }}</strong></td>
                    <td>{{ $santriKelas->santri->nama_lengkap }}</td>
                    <td>
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> {{ $santriKelas->santri->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.santri.show', $santriKelas->santri->id_santri) }}" 
                           class="btn btn-sm btn-info"
                           title="Lihat Detail">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="content-box" style="margin-top: 20px;">
    <div class="text-center py-5">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Belum ada santri di kelas ini</h5>
        <p class="text-muted">Kelas ini belum memiliki santri untuk tahun ajaran {{ $tahunAjaranAktif }}.</p>
    </div>
</div>
@endif

@endsection
