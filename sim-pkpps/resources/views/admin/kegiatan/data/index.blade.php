{{-- views/admin/kegiatan/data/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan Santri</h2>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Tambah Kegiatan
        </a>
        <a href="{{ route('admin.kategori-kegiatan.index') }}" class="btn btn-info btn-sm">
                <i class="fas fa-tags"></i> Kategori
            </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- Filter --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" class="filter-form-inline">
        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-calendar-day"></i> Hari:
            </label>
            <select name="hari" class="form-control" onchange="this.form.submit()" style="max-width: 160px;">
                <option value="">Semua Hari</option>
                @foreach($hariList as $hari)
                    <option value="{{ $hari }}" {{ request('hari') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-tags"></i> Kategori:
            </label>
            <select name="kategori_id" class="form-control" onchange="this.form.submit()" style="max-width: 180px;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->kategori_id }}" {{ request('kategori_id') == $kat->kategori_id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-school"></i> Kelas:
            </label>
            <select name="kelas_id" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
                <option value="">Semua Kelas</option>
                <option value="umum" {{ request('kelas_id') === 'umum' ? 'selected' : '' }}>Kegiatan Umum</option>
                @foreach($kelasList->groupBy('kelompok.nama_kelompok') as $kelompokNama => $kelasGroup)
                    <optgroup label="{{ $kelompokNama }}">
                        @foreach($kelasGroup as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-search"></i>
            </label>
            <input type="text" name="search" class="form-control" placeholder="Cari kegiatan..."
                   value="{{ request('search') }}" style="max-width: 180px;">
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i> Filter
        </button>

        @if(request()->hasAny(['hari', 'kategori_id', 'kelas_id', 'search']))
            <a href="{{ route('admin.kegiatan.jadwal') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- Jadwal per Hari --}}
@php
    $kegiatanPerHari = $kegiatans->groupBy('hari');
    $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
@endphp

@if($kegiatans->count() > 0)
    @foreach($urutanHari as $hari)
        @php
            $kegiatanHari = $kegiatanPerHari->get($hari, collect());
        @endphp
        @if($kegiatanHari->count() > 0)
            <div class="content-box" style="margin-bottom: 16px;">
                <h4 style="margin: 0 0 14px; color: var(--primary-color); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-calendar-day"></i> {{ $hari }}
                    <span class="badge badge-primary">{{ $kegiatanHari->count() }} kegiatan</span>
                </h4>

                <div class="table-wrapper">

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Kegiatan</th>
                            <th style="width: 130px;">Waktu</th>
                            <th style="width: 140px;">Kategori</th>
                            <th>Kelas</th>
                            <th style="width: 100px;">Materi</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kegiatanHari->sortBy('waktu_mulai') as $index => $kegiatan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
                            <td>
                                <i class="fas fa-clock" style="color: var(--primary-color);"></i>
                                {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} &ndash; {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $kegiatan->kategori->nama_kategori }}</span>
                            </td>
                            <td>
                                @if($kegiatan->kelasKegiatan->isEmpty())
                                    <span class="badge badge-secondary"><i class="fas fa-users"></i> Umum</span>
                                @else
                                    @foreach($kegiatan->kelasKegiatan->take(3) as $kls)
                                        <span class="badge badge-primary">{{ $kls->nama_kelas }}</span>
                                    @endforeach
                                    @if($kegiatan->kelasKegiatan->count() > 3)
                                        <span class="badge badge-light">+{{ $kegiatan->kelasKegiatan->count() - 3 }}</span>
                                    @endif
                                @endif
                            </td>
                            <td>{{ Str::limit($kegiatan->materi, 30) ?: '-' }}</td>
                            <td class="text-center">
                                <div style="display: flex; gap: 4px; justify-content: center;">
                                    <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.kegiatan.show', $kegiatan) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.kegiatan.destroy', $kegiatan) }}" method="POST"
                                          onsubmit="return confirm('Yakin hapus kegiatan ini?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                </div>
            </div>
        @endif
    @endforeach

    <div style="margin-top: 14px;">
        {{ $kegiatans->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>Belum Ada Jadwal Kegiatan</h3>
        <p>Silakan tambahkan kegiatan terlebih dahulu.</p>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Kegiatan
        </a>
    </div>
@endif
@endsection