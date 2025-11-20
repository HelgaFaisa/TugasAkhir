@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> Detail Transaksi Uang Saku</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3>{{ $transaksi->id_uang_saku }}</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.uang-saku.riwayat', $transaksi->id_santri) }}" class="btn btn-primary">
                <i class="fas fa-history"></i> Lihat Riwayat
            </a>
            <a href="{{ route('admin.uang-saku.edit', $transaksi->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.uang-saku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-file-alt"></i> Informasi Transaksi</h4>
        <table class="detail-table">
            <tr>
                <th>ID Transaksi</th>
                <td><strong>{{ $transaksi->id_uang_saku }}</strong></td>
            </tr>
            <tr>
                <th>Santri</th>
                <td>
                    <strong>{{ $transaksi->santri->nama_lengkap }}</strong><br>
                    <small class="text-muted">{{ $transaksi->santri->id_santri }} - {{ $transaksi->santri->kelas }}</small>
                </td>
            </tr>
            <tr>
                <th>Jenis Transaksi</th>
                <td>
                    @if($transaksi->jenis_transaksi === 'pemasukan')
                        <span class="badge badge-success badge-lg">
                            <i class="fas fa-arrow-down"></i> Pemasukan
                        </span>
                    @else
                        <span class="badge badge-danger badge-lg">
                            <i class="fas fa-arrow-up"></i> Pengeluaran
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Nominal</th>
                <td class="nominal-highlight" style="font-size: 1.3rem;">
                    {{ $transaksi->nominal_format }}
                </td>
            </tr>
            <tr>
                <th>Tanggal Transaksi</th>
                <td>{{ $transaksi->tanggal_transaksi->format('d F Y') }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $transaksi->keterangan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-calculator"></i> Rincian Saldo</h4>
        <table class="detail-table">
            <tr>
                <th>Saldo Sebelum</th>
                <td>
                    <strong>Rp {{ number_format($transaksi->saldo_sebelum, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <th>{{ $transaksi->jenis_transaksi === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}</th>
                <td style="color: {{ $transaksi->jenis_transaksi === 'pemasukan' ? '#6FBA9D' : '#FF8B94' }};">
                    <strong>
                        {{ $transaksi->jenis_transaksi === 'pemasukan' ? '+' : '-' }} 
                        {{ $transaksi->nominal_format }}
                    </strong>
                </td>
            </tr>
            <tr style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%);">
                <th>Saldo Sesudah</th>
                <td>
                    <strong style="font-size: 1.2rem; color: {{ $transaksi->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94' }}">
                        {{ $transaksi->saldo_sesudah_format }}
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-clock"></i> Informasi Waktu</h4>
        <table class="detail-table">
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $transaksi->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <th>Terakhir Diubah</th>
                <td>{{ $transaksi->updated_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- Action Buttons Bottom --}}
    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; flex-wrap: wrap;">
        <a href="{{ route('admin.uang-saku.riwayat', $transaksi->id_santri) }}" class="btn btn-primary">
            <i class="fas fa-history"></i> Lihat Riwayat
        </a>
        <a href="{{ route('admin.uang-saku.edit', $transaksi->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Transaksi
        </a>
        <form action="{{ route('admin.uang-saku.destroy', $transaksi->id) }}" 
              method="POST" 
              style="display: inline;"
              onsubmit="return confirm('Yakin ingin menghapus transaksi ini?\n\nPerhatian: Saldo transaksi setelahnya akan di-recalculate otomatis.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>
@endsection