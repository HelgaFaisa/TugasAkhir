@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> Data Capaian Santri</h2>
</div>

{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

{{-- Action Button --}}
<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('admin.capaian.create') }}" class="btn btn-success" style="padding: 9px 18px;">
            <i class="fas fa-plus"></i> Input Capaian
        </a>
        <a href="{{ route('admin.capaian.akses-santri') }}" class="btn btn-primary" style="padding: 9px 18px;">
            <i class="fas fa-unlock-alt"></i> Kelola Akses Input Santri
        </a>
    </div>
</div>

{{-- Filter Section --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="{{ route('admin.capaian.index') }}" class="filter-form-inline">
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            {{-- Filter Kelas (Dropdown dynamic dari database) --}}
            <select name="id_kelas" class="form-control" style="width: 220px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @php
                    $kelompokGrouped = $kelasList->groupBy(fn($k) => $k->kelompok->nama_kelompok ?? 'Lainnya');
                @endphp
                @foreach($kelompokGrouped as $namaKelompok => $kelasGroup)
                    <optgroup label="{{ $namaKelompok }}">
                        @foreach($kelasGroup as $kls)
                            <option value="{{ $kls->id }}" {{ $selectedKelas == $kls->id ? 'selected' : '' }}>
                                {{ $kls->nama_kelas }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            {{-- Semester Filter --}}
            <select name="id_semester" class="form-control" style="width: 250px;">
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id_semester }}" {{ $selectedSemester == $semester->id_semester ? 'selected' : '' }}>
                        {{ $semester->nama_semester }}
                    </option>
                @endforeach
            </select>

            {{-- Search Input --}}
            <input type="text" name="search" class="form-control" placeholder="Cari nama santri / NIS..." 
                   value="{{ $search ?? '' }}" style="width: 300px;">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>

            @if($selectedKelas || $search)
                <a href="{{ route('admin.capaian.index', ['id_semester' => $selectedSemester]) }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Content Section --}}
<div class="content-box">
    @if($selectedKelas)
        @php $selectedKelasObj = $kelasList->firstWhere('id', $selectedKelas); @endphp
        <div style="margin-bottom: 15px; padding: 12px 15px; background: #e3f2fd; border-left: 4px solid #2196F3; border-radius: 4px;">
            <span style="color: #1976D2; font-weight: 600;">
                <i class="fas fa-filter"></i> Menampilkan data kelas: <strong>{{ $selectedKelasObj->nama_kelas ?? 'Unknown' }}</strong>
                @if($selectedKelasObj && $selectedKelasObj->kelompok)
                    ({{ $selectedKelasObj->kelompok->nama_kelompok }})
                @endif
            </span>
        </div>
    @endif
    
    @if($santriData->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">NIS</th>
                    <th style="width: 30%;">Nama Santri</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 15%;">Total Materi</th>
                    <th style="width: 15%;">Total Progress</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($santriData as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $data['santri']->nis }}</strong></td>
                        <td>{{ $data['santri']->nama_lengkap }}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $data['santri']->kelas }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $data['total_materi'] }} materi</span>
                        </td>
                        <td>
                            @php
                                $progress = $data['total_progress'];
                                if ($progress >= 100) {
                                    $badgeClass = 'badge-success';
                                    $icon = 'fa-check-circle';
                                } elseif ($progress >= 75) {
                                    $badgeClass = 'badge-primary';
                                    $icon = 'fa-battery-three-quarters';
                                } elseif ($progress >= 50) {
                                    $badgeClass = 'badge-warning';
                                    $icon = 'fa-battery-half';
                                } elseif ($progress >= 25) {
                                    $badgeClass = 'badge-danger';
                                    $icon = 'fa-battery-quarter';
                                } else {
                                    $badgeClass = 'badge-secondary';
                                    $icon = 'fa-battery-empty';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                <i class="fas {{ $icon }}"></i> {{ number_format($progress, 2) }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.capaian.riwayat-santri', ['id_santri' => $data['santri']->id_santri, 'id_semester' => $selectedSemester]) }}" 
                               class="btn btn-sm btn-primary" title="Lihat Detail Capaian">
                                <i class="fas fa-eye"></i> Show
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Tidak Ada Data</h3>
            <p>
                @if($search)
                    Tidak ditemukan santri dengan kata kunci "<strong>{{ $search }}</strong>".
                @else
                    Belum ada santri dengan data capaian.
                @endif
            </p>
            @if($search || $selectedKelas)
                <a href="{{ route('admin.capaian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            @endif
        </div>
    @endif
</div>
@endsection