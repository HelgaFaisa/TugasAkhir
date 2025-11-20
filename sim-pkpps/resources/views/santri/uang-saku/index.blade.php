{{-- resources/views/santri/uang-saku/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Uang Saku')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-wallet"></i> Riwayat Uang Saku</h2>
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

{{-- Statistik Cards --}}
<div class="row-cards">
    <div class="card card-success">
        <h3><i class="fas fa-arrow-down"></i> Total Pemasukan</h3>
        <div class="card-value">{{ 'Rp ' . number_format($totalPemasukan, 0, ',', '.') }}</div>
        <div class="card-icon"><i class="fas fa-arrow-down"></i></div>
    </div>
    
    <div class="card card-danger">
        <h3><i class="fas fa-arrow-up"></i> Total Pengeluaran</h3>
        <div class="card-value">{{ 'Rp ' . number_format($totalPengeluaran, 0, ',', '.') }}</div>
        <div class="card-icon"><i class="fas fa-arrow-up"></i></div>
    </div>
    
    <div class="card card-primary">
        <h3><i class="fas fa-wallet"></i> Saldo Terakhir</h3>
        <div class="card-value">{{ 'Rp ' . number_format($saldoTerakhir, 0, ',', '.') }}</div>
        <div class="card-icon"><i class="fas fa-wallet"></i></div>
    </div>
</div>

{{-- Filter Form --}}
<div class="content-box" style="margin-top: 20px;">
    <form method="GET" action="{{ route('santri.uang-saku.index') }}" class="filter-form-inline">
        <input type="text" name="search" class="form-control" placeholder="Cari keterangan..." value="{{ request('search') }}" style="min-width: 200px;">
        
        <select name="jenis_transaksi" class="form-control" style="min-width: 150px;">
            <option value="">Semua Jenis</option>
            <option value="pemasukan" {{ request('jenis_transaksi') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
            <option value="pengeluaran" {{ request('jenis_transaksi') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
        </select>
        
        <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}" placeholder="Dari Tanggal">
        
        <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}" placeholder="Sampai Tanggal">
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <a href="{{ route('santri.uang-saku.index') }}" class="btn btn-secondary">
            <i class="fas fa-redo"></i> Reset
        </a>
    </form>
</div>

{{-- Tabel Riwayat --}}
<div class="table-container" style="margin-top: 20px;">
    @if($riwayatUangSaku->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Saldo Sebelum</th>
                    <th>Saldo Sesudah</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayatUangSaku as $index => $transaksi)
                <tr>
                    <td>{{ $riwayatUangSaku->firstItem() + $index }}</td>
                    <td><strong>{{ $transaksi->id_uang_saku }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y') }}</td>
                    <td>
                        @if($transaksi->jenis_transaksi === 'pemasukan')
                            <span class="badge badge-success">
                                <i class="fas fa-arrow-down"></i> Pemasukan
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fas fa-arrow-up"></i> Pengeluaran
                            </span>
                        @endif
                    </td>
                    <td class="nominal-highlight">{{ 'Rp ' . number_format($transaksi->nominal, 0, ',', '.') }}</td>
                    <td>{{ $transaksi->keterangan ?? '-' }}</td>
                    <td>{{ 'Rp ' . number_format($transaksi->saldo_sebelum, 0, ',', '.') }}</td>
                    <td>{{ 'Rp ' . number_format($transaksi->saldo_sesudah, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ route('santri.uang-saku.show', $transaksi->id) }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $riwayatUangSaku->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Belum Ada Transaksi</h3>
            <p>Riwayat uang saku Anda masih kosong.</p>
        </div>
    @endif
</div>
@endsection