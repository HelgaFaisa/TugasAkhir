@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> Detail Transaksi Keuangan</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3>{{ $transaksi->id_keuangan }}</h3>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.keuangan.edit', $transaksi->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('admin.keuangan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-file-alt"></i> Informasi Transaksi</h4>
        <table class="detail-table">
            <tr>
                <th>ID Transaksi</th>
                <td><strong>{{ $transaksi->id_keuangan }}</strong></td>
            </tr>
            <tr>
                <th>Jenis</th>
                <td>
                    @if($transaksi->jenis === 'pemasukan')
                        <span class="badge badge-success badge-lg"><i class="fas fa-arrow-down"></i> Pemasukan</span>
                    @else
                        <span class="badge badge-danger badge-lg"><i class="fas fa-arrow-up"></i> Pengeluaran</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Nominal</th>
                <td class="nominal-highlight" style="font-size:1.3rem;">{{ $transaksi->nominal_format }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ $transaksi->tanggal->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $transaksi->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Dibuat</th>
                <td>{{ $transaksi->created_at->translatedFormat('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
