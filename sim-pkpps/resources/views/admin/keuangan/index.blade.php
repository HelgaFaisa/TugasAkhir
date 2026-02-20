@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-cash-register"></i> Kas & Keuangan Pondok</h2>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <a href="{{ route('admin.keuangan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </a>
        <a href="{{ route('admin.keuangan.laporan') }}" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Laporan Neraca
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.keuangan.index') }}" id="filterForm" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items: end;">
            <div class="form-group" style="margin-bottom:0;">
                <input type="text" name="search" class="form-control" placeholder="Cari ID / keterangan..."
                       value="{{ request('search') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <select name="jenis" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan" {{ request('jenis')=='pemasukan'?'selected':'' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('jenis')=='pengeluaran'?'selected':'' }}>Pengeluaran</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <select name="bulan" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan')==$i?'selected':'' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <input type="number" name="tahun" class="form-control" placeholder="Tahun"
                       value="{{ request('tahun', date('Y')) }}" min="2020" max="2100">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filter</button>
                @if(request()->hasAny(['search','jenis','bulan','tahun']))
                    <a href="{{ route('admin.keuangan.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i></a>
                @endif
            </div>
        </div>
    </form>

    @if($transaksi->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:10%;">ID</th>
                    <th style="width:12%;">Tanggal</th>
                    <th style="width:10%;">Jenis</th>
                    <th style="width:15%;">Nominal</th>
                    <th>Keterangan</th>
                    <th style="width:10%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi as $i => $item)
                <tr>
                    <td>{{ $transaksi->firstItem() + $i }}</td>
                    <td><strong>{{ $item->id_keuangan }}</strong></td>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>
                        @if($item->jenis === 'pemasukan')
                            <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Keluar</span>
                        @endif
                    </td>
                    <td class="nominal-highlight">{{ $item->nominal_format }}</td>
                    <td><div class="content-preview">{{ $item->keterangan ?? '-' }}</div></td>
                    <td class="text-center">
                        <div style="display:flex; gap:4px; justify-content:center;">
                            <a href="{{ route('admin.keuangan.show', $item->id) }}" class="btn btn-primary btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.keuangan.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.keuangan.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $transaksi->links() }}</div>
    @else
        <div class="empty-state">
            <i class="fas fa-cash-register"></i>
            <h3>Belum Ada Transaksi</h3>
            <p>Tambahkan transaksi keuangan pondok pertama.</p>
            <a href="{{ route('admin.keuangan.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</a>
        </div>
    @endif
</div>
@endsection
