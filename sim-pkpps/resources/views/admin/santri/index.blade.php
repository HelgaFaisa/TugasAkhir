@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Data Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-users"></i> Data Santri</h2>
</div>

@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="content-box">
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
        <!-- Tombol Tambah -->
        <a href="{{ route('admin.santri.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Santri
        </a>
        
        <!-- Form Search & Filter -->
        <form action="{{ route('admin.santri.index') }}" method="GET" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, NIS, atau ID..." value="{{ request('search') }}" style="width: 220px; height: 38px;">
            
            <select name="status" class="form-control" style="width: 130px; height: 38px;">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            
            <select name="kelas" class="form-control" style="width: 130px; height: 38px;">
                <option value="">Semua Kelas</option>
                <option value="PB" {{ request('kelas') == 'PB' ? 'selected' : '' }}>PB</option>
                <option value="Lambatan" {{ request('kelas') == 'Lambatan' ? 'selected' : '' }}>Lambatan</option>
                <option value="Cepatan" {{ request('kelas') == 'Cepatan' ? 'selected' : '' }}>Cepatan</option>
            </select>
            
            <button type="submit" class="btn btn-primary btn-sm" style="height: 38px; padding: 0 16px;">
                <i class="fas fa-search"></i> Cari
            </button>
            
            @if(request('search') || request('status') || request('kelas'))
                <a href="{{ route('admin.santri.index') }}" class="btn btn-secondary btn-sm" style="height: 38px; padding: 0 16px; display: inline-flex; align-items: center;">
                    <i class="fas fa-redo"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Santri</th>
                <th>NIS</th>
                <th>Nama Lengkap</th>
                <th>Jenis Kelamin</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($santris as $index => $santri)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $santri->id_santri }}</strong></td>
                <td>{{ $santri->nis ?? '-' }}</td>
                <td>{{ $santri->nama_lengkap }}</td>
                <td>{{ $santri->jenis_kelamin }}</td>
                <td><strong>{{ $santri->kelas }}</strong></td>
                <td>
                    @if($santri->status == 'Aktif')
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); color: #2C5F4F; display: inline-block;">
                            <i class="fas fa-check-circle"></i> {{ $santri->status }}
                        </span>
                    @elseif($santri->status == 'Lulus')
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); color: #2D4A7C; display: inline-block;">
                            <i class="fas fa-graduation-cap"></i> {{ $santri->status }}
                        </span>
                    @else
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: linear-gradient(135deg, #E8ECF0 0%, #D1D8E0 100%); color: #555; display: inline-block;">
                            <i class="fas fa-times-circle"></i> {{ $santri->status }}
                        </span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.santri.show', $santri) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.santri.edit', $santri) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.santri.destroy', $santri) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data santri {{ $santri->nama_lengkap }}?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 40px;">
                    <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 15px; display: block;"></i>
                    @if(request('search') || request('status') || request('kelas'))
                        <strong>Data tidak ditemukan.</strong><br>
                        <small>Coba ubah kata kunci pencarian atau filter yang digunakan.</small>
                    @else
                        <strong>Belum ada data santri.</strong><br>
                        <small>Klik tombol "Tambah Santri" untuk menambahkan data baru.</small>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($santris->count() > 0)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #E8F7F2;">
            <p style="color: #7F8C8D; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> 
                Menampilkan <strong>{{ $santris->count() }}</strong> data santri
                @if(request('search') || request('status') || request('kelas'))
                    dari hasil pencarian/filter
                @endif
            </p>
        </div>
    @endif

    <!-- Pagination (jika menggunakan paginate) -->
    @if(method_exists($santris, 'links'))
        <div style="margin-top: 20px;">
            {{ $santris->links() }}
        </div>
    @endif
</div>
@endsection