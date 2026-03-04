@extends('layouts.app')

@section('content')
@php
    $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
@endphp

<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Laporan Neraca Keuangan</h2>
    <p>Periode: {{ $namaBulan }} {{ $tahun }}</p>
</div>

{{-- Filter Periode --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="{{ route('admin.keuangan.laporan') }}" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;">
            <label>Bulan</label>
            <select name="bulan" class="form-control">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan==$i?'selected':'' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label>Tahun</label>
            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2020" max="2100" style="width:100px;">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
        <a href="{{ route('admin.keuangan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </form>
</div>

{{-- Ringkasan Neraca --}}
<div class="row-cards" style="grid-template-columns: repeat(4, 1fr);">
    <div class="card card-info">
        <h3>SPP Terkumpul</h3>
        <p class="card-value-small">Rp {{ number_format($sppTerkumpul, 0, ',', '.') }}</p>
        <i class="fas fa-file-invoice-dollar card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Pemasukan Lain</h3>
        <p class="card-value-small">Rp {{ number_format($pemasukanPondok, 0, ',', '.') }}</p>
        <i class="fas fa-arrow-down card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Total Pengeluaran</h3>
        <p class="card-value-small">Rp {{ number_format($pengeluaranPondok, 0, ',', '.') }}</p>
        <i class="fas fa-arrow-up card-icon"></i>
    </div>
    <div class="card {{ $sisaKas >= 0 ? 'card-primary' : 'card-danger' }}">
        <h3>Sisa Kas</h3>
        <p class="card-value-small">Rp {{ number_format($sisaKas, 0, ',', '.') }}</p>
        <i class="fas fa-wallet card-icon"></i>
    </div>
</div>

{{-- Detail Tabel --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:24px;">

    {{-- Pengeluaran Terbesar --}}
    <div class="content-box">
        <h4 style="margin-bottom:12px;"><i class="fas fa-arrow-up" style="color:var(--danger-color);"></i> Pengeluaran Terbesar</h4>
        @if($detailPengeluaran->count() > 0)
            <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Nominal</th></tr></thead>
                <tbody>
                    @foreach($detailPengeluaran as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d/m') }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="nominal-highlight">{{ $item->nominal_format }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @else
            <p class="text-muted">Tidak ada pengeluaran bulan ini.</p>
        @endif
    </div>

    {{-- Pemasukan Non-SPP --}}
    <div class="content-box">
        <h4 style="margin-bottom:12px;"><i class="fas fa-arrow-down" style="color:var(--success-color);"></i> Pemasukan Non-SPP</h4>
        @if($detailPemasukan->count() > 0)
            <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Nominal</th></tr></thead>
                <tbody>
                    @foreach($detailPemasukan as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d/m') }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="nominal-highlight">{{ $item->nominal_format }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @else
            <p class="text-muted">Tidak ada pemasukan non-SPP bulan ini.</p>
        @endif
    </div>
</div>
@endsection
