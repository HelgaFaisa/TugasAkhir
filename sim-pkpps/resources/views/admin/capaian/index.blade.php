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

{{-- Filter & Search Section --}}
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.capaian.index') }}" class="filter-form-inline">
        <select name="id_santri" class="form-control" style="width: 250px;">
            <option value="">Semua Santri</option>
            @foreach($santris as $santri)
                <option value="{{ $santri->id_santri }}" {{ request('id_santri') == $santri->id_santri ? 'selected' : '' }}>
                    {{ $santri->nama_lengkap }} ({{ $santri->kelas }})
                </option>
            @endforeach
        </select>

        <select name="id_semester" class="form-control" style="width: 200px;">
            <option value="">Semua Semester</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id_semester }}" {{ request('id_semester') == $semester->id_semester ? 'selected' : '' }}>
                    {{ $semester->nama_semester }}
                </option>
            @endforeach
        </select>

        <select name="kategori" class="form-control" style="width: 180px;">
            <option value="">Semua Kategori</option>
            <option value="Al-Qur'an" {{ request('kategori') == 'Al-Qur\'an' ? 'selected' : '' }}>Al-Qur'an</option>
            <option value="Hadist" {{ request('kategori') == 'Hadist' ? 'selected' : '' }}>Hadist</option>
            <option value="Materi Tambahan" {{ request('kategori') == 'Materi Tambahan' ? 'selected' : '' }}>Materi Tambahan</option>
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>

        @if(request()->anyFilled(['id_santri', 'id_semester', 'kategori']))
            <a href="{{ route('admin.capaian.index') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        @endif

        <a href="{{ route('admin.capaian.create') }}" class="btn btn-success" style="margin-left: auto;">
            <i class="fas fa-plus"></i> Input Capaian
        </a>
    </form>
</div>

{{-- Table Section --}}
<div class="content-box">
    @if($capaians->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Santri</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 20%;">Materi</th>
                    <th style="width: 10%;">Kategori</th>
                    <th style="width: 15%;">Semester</th>
                    <th style="width: 10%;">Halaman</th>
                    <th style="width: 10%;">Progress</th>
                    <th class="text-center" style="width: 5%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($capaians as $index => $capaian)
                    <tr>
                        <td>{{ $capaians->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $capaian->santri->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $capaian->santri->nis }}</small>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $capaian->santri->kelas }}</span>
                        </td>
                        <td>
                            <strong>{{ $capaian->materi->nama_kitab }}</strong>
                        </td>
                        <td>{!! $capaian->materi->kategori_badge !!}</td>
                        <td>
                            <small>{{ $capaian->semester->nama_semester }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">
                                {{ $capaian->jumlah_halaman_selesai }} / {{ $capaian->materi->total_halaman }}
                            </span>
                        </td>
                        <td>{!! $capaian->persentase_badge !!}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.capaian.show', $capaian) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.capaian.edit', $capaian) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.capaian.destroy', $capaian) }}" 
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Yakin ingin menghapus capaian ini?')">
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

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $capaians->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <h3>Belum Ada Data Capaian</h3>
            <p>Silakan input capaian santri terlebih dahulu.</p>
            <a href="{{ route('admin.capaian.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Input Capaian Pertama
            </a>
        </div>
    @endif
</div>
@endsection