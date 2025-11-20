@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-wallet"></i> Manajemen Uang Saku Santri</h2>
</div>

@if(session('success'))
    <div class="alert alert-success" id="success-alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" id="error-alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

{{-- Main Content --}}
<div class="content-box">
    {{-- Header Actions --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.uang-saku.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <form method="GET" action="{{ route('admin.uang-saku.index') }}" id="filterForm" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Cari ID, santri, atau keterangan..." 
                       value="{{ request('search') }}"
                       id="searchInput">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="id_santri" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Santri</option>
                    @foreach($santriList as $santri)
                        <option value="{{ $santri->id_santri }}" {{ request('id_santri') == $santri->id_santri ? 'selected' : '' }}>
                            {{ $santri->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <select name="jenis_transaksi" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan" {{ request('jenis_transaksi') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('jenis_transaksi') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <input type="date" 
                       name="tanggal_dari" 
                       class="form-control" 
                       placeholder="Dari Tanggal" 
                       value="{{ request('tanggal_dari') }}"
                       onchange="document.getElementById('filterForm').submit();">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <input type="date" 
                       name="tanggal_sampai" 
                       class="form-control" 
                       placeholder="Sampai Tanggal" 
                       value="{{ request('tanggal_sampai') }}"
                       onchange="document.getElementById('filterForm').submit();">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                
                @if(request()->hasAny(['search', 'id_santri', 'jenis_transaksi', 'tanggal_dari', 'tanggal_sampai']))
                    <a href="{{ route('admin.uang-saku.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Tabel Transaksi --}}
    @if($transaksi->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">ID Transaksi</th>
                    <th style="width: 15%;">Santri</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 10%;">Jenis</th>
                    <th style="width: 12%;">Nominal</th>
                    <th style="width: 20%;">Keterangan</th>
                    <th style="width: 12%;">Saldo</th>
                    <th style="width: 6%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi as $index => $item)
                    <tr>
                        <td>{{ $transaksi->firstItem() + $index }}</td>
                        <td><strong>{{ $item->id_uang_saku }}</strong></td>
                        <td>
                            <a href="{{ route('admin.uang-saku.riwayat', $item->id_santri) }}" 
                               class="link-primary"
                               title="Lihat Riwayat">
                                {{ $item->santri->nama_lengkap }}
                            </a>
                        </td>
                        <td>{{ $item->tanggal_transaksi->format('d/m/Y') }}</td>
                        <td>
                            @if($item->jenis_transaksi === 'pemasukan')
                                <span class="badge badge-success">
                                    <i class="fas fa-arrow-down"></i> Pemasukan
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-arrow-up"></i> Pengeluaran
                                </span>
                            @endif
                        </td>
                        <td class="nominal-highlight">
                            {{ $item->nominal_format }}
                        </td>
                        <td>
                            <div class="content-preview">
                                {{ $item->keterangan ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <strong style="color: {{ $item->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94' }}">
                                {{ $item->saldo_sesudah_format }}
                            </strong>
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <a href="{{ route('admin.uang-saku.show', $item->id) }}" 
                                   class="btn btn-primary btn-sm" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.uang-saku.edit', $item->id) }}" 
                                   class="btn btn-warning btn-sm" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.uang-saku.destroy', $item->id) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $transaksi->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-wallet"></i>
            <h3>Belum Ada Transaksi</h3>
            <p>Belum ada transaksi uang saku yang tercatat. Tambahkan transaksi pertama!</p>
            <a href="{{ route('admin.uang-saku.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    @endif
</div>

<script>
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        const successAlert = document.getElementById('success-alert');
        const errorAlert = document.getElementById('error-alert');
        
        if (successAlert) {
            successAlert.style.transition = 'opacity 0.5s ease';
            successAlert.style.opacity = '0';
            setTimeout(() => successAlert.remove(), 500);
        }
        
        if (errorAlert) {
            errorAlert.style.transition = 'opacity 0.5s ease';
            errorAlert.style.opacity = '0';
            setTimeout(() => errorAlert.remove(), 500);
        }
    }, 5000);

    // Auto submit on search (debounce)
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 800);
    });
</script>
@endsection