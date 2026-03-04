@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Manajemen Semester</h2>
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

{{-- Action Buttons --}}
<div class="content-header-flex">
    <a href="{{ route('admin.semester.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Semester
    </a>
</div>

{{-- Table Section --}}
<div class="content-box">
    @if($semesters->count() > 0)
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">ID Semester</th>
                    <th style="width: 25%;">Nama Semester</th>
                    <th style="width: 15%;">Tahun Ajaran</th>
                    <th style="width: 10%;">Periode</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 10%;">Status</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semesters as $index => $semester)
                    <tr>
                        <td>{{ $semesters->firstItem() + $index }}</td>
                        <td><strong>{{ $semester->id_semester }}</strong></td>
                        <td>{{ $semester->nama_semester }}</td>
                        <td>{{ $semester->tahun_ajaran }}</td>
                        <td class="text-center">
                            <span class="badge {{ $semester->periode == 1 ? 'badge-info' : 'badge-warning' }}">
                                Semester {{ $semester->periode }}
                            </span>
                        </td>
                        <td>
                            <small>
                                {{ $semester->tanggal_mulai->format('d/m/Y') }} -<br>
                                {{ $semester->tanggal_akhir->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>{!! $semester->status_badge !!}</td>
                        <td class="text-center">
                            <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                                <a href="{{ route('admin.semester.show', $semester) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.semester.edit', $semester) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.semester.destroy', $semester) }}" 
                                      method="POST" style="margin: 0;"
                                      onsubmit="return confirm('Yakin ingin menghapus semester ini?')">
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

        {{-- Pagination --}}
        <div style="margin-top: 14px;">
            {{ $semesters->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Belum Ada Semester</h3>
            <p>Silakan tambahkan semester terlebih dahulu sebelum mengelola capaian santri.</p>
            <a href="{{ route('admin.semester.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Semester Pertama
            </a>
        </div>
    @endif
</div>
@endsection