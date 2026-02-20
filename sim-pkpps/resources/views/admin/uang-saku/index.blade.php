@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-wallet"></i> Manajemen Uang Saku Santri</h2>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<div class="content-box">
    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <a href="{{ route('admin.uang-saku.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </a>
        <form method="GET" action="{{ route('admin.uang-saku.index') }}" style="display:flex; gap:8px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama / ID santri..."
                   value="{{ request('search') }}" style="width:250px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
            @if(request('search'))
                <a href="{{ route('admin.uang-saku.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i></a>
            @endif
        </form>
    </div>

    {{-- Grouped Santri List --}}
    @if($santriList->count() > 0)
        @foreach($santriList as $santri)
        <div class="content-box" style="margin-bottom:12px; padding:16px;">
            {{-- Baris utama --}}
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; cursor:pointer;"
                 onclick="toggleDetail('detail-{{ $santri->id_santri }}', this)">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-chevron-right toggle-arrow" style="transition:transform .2s; color:var(--text-light);"></i>
                    <div>
                        <strong>{{ $santri->nama_lengkap }}</strong>
                        <small class="text-muted" style="margin-left:6px;">{{ $santri->id_santri }}</small>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                    <span style="font-weight:700; font-size:1.1rem; color:{{ $santri->saldo_terakhir >= 0 ? '#6FBA9D' : '#FF8B94' }};">
                        Rp {{ number_format($santri->saldo_terakhir, 0, ',', '.') }}
                    </span>
                    <span class="badge badge-info">{{ $santri->transaksi_bulan_ini }} transaksi bln ini</span>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.uang-saku.riwayat', $santri->id_santri) }}" class="btn btn-primary btn-sm" title="Riwayat Lengkap" onclick="event.stopPropagation();">
                            <i class="fas fa-history"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Detail transaksi (collapsed) --}}
            <div id="detail-{{ $santri->id_santri }}" style="display:none; margin-top:12px; border-top:1px solid var(--primary-light); padding-top:12px;">
                @if($santri->transaksi_terbaru->isNotEmpty())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Saldo</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($santri->transaksi_terbaru as $tx)
                            <tr>
                                <td>{{ $tx->tanggal_transaksi->format('d/m/Y') }}</td>
                                <td>
                                    @if($tx->jenis_transaksi === 'pemasukan')
                                        <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Keluar</span>
                                    @endif
                                </td>
                                <td class="nominal-highlight">{{ $tx->nominal_format }}</td>
                                <td><div class="content-preview">{{ $tx->keterangan ?? '-' }}</div></td>
                                <td style="color:{{ $tx->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94' }}; font-weight:600;">
                                    Rp {{ number_format($tx->saldo_sesudah, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div style="display:flex; gap:4px; justify-content:center;">
                                        <a href="{{ route('admin.uang-saku.show', $tx->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.uang-saku.edit', $tx->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.uang-saku.destroy', $tx->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($santri->transaksi_terbaru->count() >= 5)
                        <div style="text-align:center; margin-top:8px;">
                            <a href="{{ route('admin.uang-saku.riwayat', $santri->id_santri) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-right"></i> Lihat Semua Riwayat
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-muted">Belum ada transaksi.</p>
                @endif
            </div>
        </div>
        @endforeach

        <div style="margin-top:20px;">{{ $santriList->links() }}</div>
    @else
        <div class="empty-state">
            <i class="fas fa-wallet"></i>
            <h3>Belum Ada Data</h3>
            <p>Belum ada santri dengan transaksi uang saku.</p>
            <a href="{{ route('admin.uang-saku.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Transaksi</a>
        </div>
    @endif
</div>

<script>
function toggleDetail(id, el) {
    var detail = document.getElementById(id);
    var arrow = el.querySelector('.toggle-arrow');
    if (detail.style.display === 'none') {
        detail.style.display = 'block';
        arrow.style.transform = 'rotate(90deg)';
    } else {
        detail.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection