{{-- resources/views/admin/pembayaran-spp/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran SPP')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-money-bill-wave"></i> Pembayaran SPP</h2>
</div>

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

<div class="content-box">
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.pembayaran-spp.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Cari santri atau ID..." 
                   value="{{ request('search') }}"
                   style="max-width: 250px;">
            
            <select name="status" class="form-control" style="max-width: 180px;">
                <option value="">Semua Status</option>
                <option value="Lunas" {{ request('status') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="Belum Lunas" {{ request('status') === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="Telat" {{ request('status') === 'Telat' ? 'selected' : '' }}>Telat</option>
            </select>

            <select name="bulan" class="form-control" style="max-width: 150px;">
                <option value="">Semua Bulan</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                    </option>
                @endfor
            </select>

            <select name="tahun" class="form-control" style="max-width: 120px;">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $tahun)
                    <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-search"></i> Cari
            </button>
            
            @if(request()->hasAny(['search', 'status', 'bulan', 'tahun']))
                <a href="{{ route('admin.pembayaran-spp.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.pembayaran-spp.generate') }}" class="btn btn-warning btn-sm hover-shadow">
                <i class="fas fa-cogs"></i> Generate SPP
            </a>
            <a href="{{ route('admin.pembayaran-spp.create') }}" class="btn btn-success btn-sm hover-shadow">
                <i class="fas fa-plus-circle"></i> Tambah Data
            </a>
        </div>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembayaran</th>
                    <th>Santri</th>
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Batas Bayar</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaranSpp as $index => $spp)
                    <tr>
                        <td>{{ $pembayaranSpp->firstItem() + $index }}</td>
                        <td><strong>{{ $spp->id_pembayaran }}</strong></td>
                        <td>
                            <strong>{{ $spp->santri->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $spp->santri->id_santri }} - {{ $spp->santri->kelas }}</small>
                        </td>
                        <td>{{ $spp->periode_lengkap }}</td>
                        <td><strong>{{ $spp->nominal_format }}</strong></td>
                        <td>
                            {{ $spp->batas_bayar->format('d/m/Y') }}
                            @if($spp->isTelat())
                                <br><small class="text-muted" style="color: #FF8B94 !important;">
                                    <i class="fas fa-exclamation-triangle"></i> Telat
                                </small>
                            @endif
                        </td>
                        <td>{!! $spp->status_badge !!}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.pembayaran-spp.show', $spp->id) }}" 
                               class="btn btn-sm btn-primary" 
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pembayaran-spp.edit', $spp->id) }}" 
                               class="btn btn-sm btn-warning" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.pembayaran-spp.destroy', $spp->id) }}" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; display: block; margin-bottom: 15px;"></i>
                            <p style="color: #999;">Tidak ada data pembayaran SPP.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pembayaranSpp->hasPages())
        <div style="margin-top: 20px;">
            {{ $pembayaranSpp->links() }}
        </div>
    @endif
</div>
@endsection