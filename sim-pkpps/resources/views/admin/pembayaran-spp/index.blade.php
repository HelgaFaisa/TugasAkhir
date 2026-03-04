{{-- resources/views/admin/pembayaran-spp/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran SPP')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-money-bill-wave"></i> Pembayaran SPP</h2>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
@endif

<div class="content-box">

    {{-- â”€â”€ Filter â”€â”€ --}}
    <div style="background:#f8f9fa;padding:14px;border-radius:8px;margin-bottom:14px;">
        <form method="GET" action="{{ route('admin.pembayaran-spp.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div style="flex:1;min-width:180px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;font-size:11px;"><i class="fas fa-search"></i> Cari Santri</label>
                <input type="text" name="search" class="form-control" placeholder="Nama, NIS, atau ID Santri..." value="{{ request('search') }}">
            </div>
            <div style="min-width:140px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;font-size:11px;"><i class="fas fa-calendar-alt"></i> Bulan</label>
                <select name="bulan" class="form-control">
                    @php $bulanIndo=[1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
                    @for($i=1;$i<=12;$i++)
                        <option value="{{ $i }}" {{ $bulan==$i?'selected':'' }}>{{ $bulanIndo[$i] }}</option>
                    @endfor
                </select>
            </div>
            <div style="min-width:110px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;font-size:11px;"><i class="fas fa-calendar"></i> Tahun</label>
                <select name="tahun" class="form-control">
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ $tahun==$thn?'selected':'' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            @if($tab==='belum-bayar')
            <div style="min-width:170px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;font-size:11px;"><i class="fas fa-filter"></i> Status</label>
                <select name="filter_status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="Belum Lunas"       {{ request('filter_status')==='Belum Lunas'?'selected':'' }}>Belum Lunas</option>
                    <option value="Telat"             {{ request('filter_status')==='Telat'?'selected':'' }}>Terlambat</option>
                    <option value="Belum Ada Tagihan" {{ request('filter_status')==='Belum Ada Tagihan'?'selected':'' }}>Belum Ada Tagihan</option>
                </select>
            </div>
            @endif
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary" style="height:38px;"><i class="fas fa-search"></i> Cari</button>
                @if(request()->hasAny(['search','filter_status']) || $bulan!=date('n') || $tahun!=date('Y'))
                    <a href="{{ route('admin.pembayaran-spp.index',['tab'=>$tab]) }}" class="btn btn-secondary" style="height:38px;"><i class="fas fa-times"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- â”€â”€ KPI Cards â”€â”€ --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:14px;">
        <div class="kpi-card" style="background:linear-gradient(135deg,#667eea,#764ba2);">
            <div><div class="kpi-label">Total Santri Aktif</div><div class="kpi-val">{{ $totalSantriAll }}</div><div class="kpi-sub">Periode ini</div></div>
            <i class="fas fa-users kpi-icon"></i>
        </div>
        <div class="kpi-card" style="background:linear-gradient(135deg,#11998e,#38ef7d);">
            <div><div class="kpi-label">Sudah Bayar</div><div class="kpi-val">{{ $totalLunas }}</div><div class="kpi-sub">Rp {{ number_format($nominalLunas,0,',','.') }}</div></div>
            <i class="fas fa-check-circle kpi-icon"></i>
        </div>
        <div class="kpi-card" style="background:linear-gradient(135deg,#9c27b0,#e040fb);">
            <div><div class="kpi-label">Cicilan</div><div class="kpi-val">{{ $totalCicilan }}</div><div class="kpi-sub">Bayar sebagian</div></div>
            <i class="fas fa-coins kpi-icon"></i>
        </div>
        <div class="kpi-card" style="background:linear-gradient(135deg,#ff6b6b,#ee5a6f);">
            <div><div class="kpi-label">Belum Bayar</div><div class="kpi-val">{{ $totalBelumBayar }}</div><div class="kpi-sub">Rp {{ number_format($nominalBelumLunas,0,',','.') }}</div></div>
            <i class="fas fa-exclamation-circle kpi-icon"></i>
        </div>
        <div class="kpi-card" style="background:linear-gradient(135deg,#ff9800,#ff5722);">
            <div><div class="kpi-label">Terlambat</div><div class="kpi-val">{{ $totalTelat }}</div><div class="kpi-sub">Melewati batas</div></div>
            <i class="fas fa-clock kpi-icon"></i>
        </div>
    </div>

    {{-- â”€â”€ Tab Navigation â”€â”€ --}}
    <div style="display:flex;gap:6px;margin-bottom:14px;border-bottom:2px solid #e0e0e0;flex-wrap:wrap;">
        <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->except('tab'),['tab'=>'belum-bayar'])) }}"
           class="spp-tab {{ $tab==='belum-bayar'?'spp-tab-danger':'spp-tab-outline-danger' }}">
            <i class="fas fa-times-circle"></i> Belum Bayar
            @if($totalBelumBayar>0)<span class="tab-badge tab-badge-danger">{{ $totalBelumBayar }}</span>@endif
        </a>
        <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->except('tab'),['tab'=>'cicilan'])) }}"
           class="spp-tab {{ $tab==='cicilan'?'spp-tab-purple':'spp-tab-outline-purple' }}">
            <i class="fas fa-coins"></i> Cicilan
            @if($totalCicilan>0)<span class="tab-badge tab-badge-purple">{{ $totalCicilan }}</span>@endif
        </a>
        <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->except('tab'),['tab'=>'sudah-bayar'])) }}"
           class="spp-tab {{ $tab==='sudah-bayar'?'spp-tab-success':'spp-tab-outline-success' }}">
            <i class="fas fa-check-circle"></i> Sudah Bayar
            @if($totalLunas>0)<span class="tab-badge tab-badge-success">{{ $totalLunas }}</span>@endif
        </a>
    </div>

    {{-- â”€â”€ Action Buttons â”€â”€ --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:8px;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.pembayaran-spp.generate') }}" class="btn btn-warning btn-sm"><i class="fas fa-cogs"></i> Generate SPP</a>
            <a href="{{ route('admin.pembayaran-spp.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Tambah Data</a>
            <a href="{{ route('admin.pembayaran-spp.laporan') }}" class="btn btn-info btn-sm"><i class="fas fa-file-pdf"></i> Cetak Laporan</a>
        </div>
        <div style="font-size:11px;color:#666;">
            Periode: <strong>{{ $bulanIndo[$bulan]??'' }} {{ $tahun }}</strong>
            @if($tab==='sudah-bayar') &nbsp;Â·&nbsp; <i class="fas fa-sort-amount-down"></i> Terbaru bayar di atas @endif
        </div>
    </div>

    {{-- â”€â”€ Table â”€â”€ --}}
    <div style="overflow-x:auto;">
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:44px;">No</th>
                    <th>ID / NIS</th>
                    <th>Nama Santri</th>
                    @if($tab==='sudah-bayar')
                        <th>Nominal</th><th>Tanggal Bayar</th><th>Status</th>
                    @elseif($tab==='cicilan')
                        <th>Tagihan</th><th>Terbayar</th><th>Sisa</th><th style="min-width:120px;">Progress</th><th>Batas Bayar</th>
                    @else
                        <th>Nominal Tagihan</th><th>Batas Bayar</th><th>Status</th>
                    @endif
                    <th class="text-center" style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($santriPaginated as $index => $item)
                <tr class="{{ $item['is_telat'] ? 'tr-telat' : '' }}">
                    <td>{{ ($currentPage-1)*20+$index+1 }}</td>
                    <td><strong>{{ $item['id_santri'] }}</strong><br><small class="text-muted">{{ $item['nis'] }}</small></td>
                    <td>
                        <strong>{{ $item['nama_lengkap'] }}</strong>
                        @if($item['is_telat'])
                            <br><span class="badge-telat"><i class="fas fa-exclamation-triangle"></i> TELAT</span>
                        @endif
                    </td>

                    @if($tab==='sudah-bayar')
                        <td><strong style="color:#28a745;">Rp {{ number_format($item['nominal'],0,',','.') }}</strong></td>
                        <td>{{ $item['tanggal_bayar'] ? \Carbon\Carbon::parse($item['tanggal_bayar'])->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge badge-success"><i class="fas fa-check-circle"></i> Lunas</span></td>

                    @elseif($tab==='cicilan')
                        @php $p=$item['pembayaran']; @endphp
                        <td><strong>Rp {{ number_format($item['nominal'],0,',','.') }}</strong></td>
                        <td style="color:#28a745;font-weight:600;">Rp {{ number_format($p->nominal_terbayar,0,',','.') }}</td>
                        <td style="color:#dc3545;font-weight:600;">Rp {{ number_format($p->nominal_sisa,0,',','.') }}</td>
                        <td>
                            <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:{{ $p->porsentase_cicilan }}%;"></div></div>
                            <small style="font-size:.7rem;color:#888;">{{ $p->porsentase_cicilan }}% terbayar</small>
                        </td>
                        <td>
                            {{ $item['batas_bayar'] ? \Carbon\Carbon::parse($item['batas_bayar'])->format('d/m/Y') : '-' }}
                            @if($item['is_telat'])<br><small style="color:#dc3545;font-weight:600;"><i class="fas fa-clock"></i> Telat {{ \Carbon\Carbon::parse($item['batas_bayar'])->diffInDays(now()) }}h</small>@endif
                        </td>

                    @else {{-- belum-bayar --}}
                        <td>
                            @if($item['pembayaran'])
                                <strong style="color:#dc3545;">Rp {{ number_format($item['nominal'],0,',','.') }}</strong>
                            @else
                                <span class="text-muted">Belum ada tagihan</span>
                            @endif
                        </td>
                        <td>
                            {{ $item['batas_bayar'] ? \Carbon\Carbon::parse($item['batas_bayar'])->format('d/m/Y') : '-' }}
                            @if($item['is_telat'])<br><small style="color:#dc3545;font-weight:600;"><i class="fas fa-clock"></i> Telat {{ \Carbon\Carbon::parse($item['batas_bayar'])->diffInDays(now()) }}h</small>@endif
                        </td>
                        <td>
                            @if($item['is_telat'])
                                <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Terlambat</span>
                            @elseif($item['status']==='Belum Lunas')
                                <span class="badge badge-warning"><i class="fas fa-clock"></i> Belum Lunas</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-info-circle"></i> Belum Ada Tagihan</span>
                            @endif
                        </td>
                    @endif

                    {{-- â”€â”€ Aksi (1 baris, tombol ikon) â”€â”€ --}}
                    <td class="text-center" style="white-space:nowrap;">
                        @if($item['pembayaran'])
                            {{-- Riwayat selalu ada --}}
                            <a href="{{ route('admin.pembayaran-spp.riwayat',$item['id_santri']) }}" class="btn btn-sm btn-info" title="Riwayat"><i class="fas fa-history"></i></a>

                            @if($tab==='sudah-bayar')
                                <a href="{{ route('admin.pembayaran-spp.cetak-bukti',$item['pembayaran']->id) }}" class="btn btn-sm btn-success" title="Cetak Bukti" target="_blank"><i class="fas fa-print"></i></a>
                                <a href="{{ route('admin.pembayaran-spp.edit',$item['pembayaran']->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>

                            @elseif($tab==='cicilan')
                                <button type="button" class="btn btn-sm btn-purple" title="Catat Cicilan"
                                    onclick="bukaCatatCicilan({{ $item['pembayaran']->id }},'{{ addslashes($item['nama_lengkap']) }}',{{ $item['nominal'] }},{{ $item['pembayaran']->nominal_terbayar }})">
                                    <i class="fas fa-coins"></i>
                                </button>
                                <form action="{{ route('admin.pembayaran-spp.bayar',$item['pembayaran']->id) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Tandai LUNAS penuh untuk {{ addslashes($item['nama_lengkap']) }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Tandai Lunas"><i class="fas fa-check-double"></i></button>
                                </form>
                                <a href="{{ route('admin.pembayaran-spp.edit',$item['pembayaran']->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>

                            @else {{-- belum-bayar --}}
                                <form action="{{ route('admin.pembayaran-spp.bayar',$item['pembayaran']->id) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Tandai Lunas untuk {{ addslashes($item['nama_lengkap']) }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Tandai Lunas"><i class="fas fa-check"></i></button>
                                </form>
                                <button type="button" class="btn btn-sm btn-purple" title="Catat Cicilan"
                                    onclick="bukaCatatCicilan({{ $item['pembayaran']->id }},'{{ addslashes($item['nama_lengkap']) }}',{{ $item['nominal'] }},0)">
                                    <i class="fas fa-coins"></i>
                                </button>
                                <a href="{{ route('admin.pembayaran-spp.edit',$item['pembayaran']->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @endif
                        @else
                            <a href="{{ route('admin.pembayaran-spp.create',['id_santri'=>$item['id_santri'],'bulan'=>$bulan,'tahun'=>$tahun]) }}"
                               class="btn btn-sm btn-primary" title="Buat Tagihan"><i class="fas fa-plus"></i> Buat</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding:28px;">
                        <i class="fas fa-inbox" style="font-size:2rem;color:#ccc;display:block;margin-bottom:10px;"></i>
                        <span style="color:#999;">
                            @if($tab==='sudah-bayar') Belum ada santri yang melunasi SPP untuk periode ini.
                            @elseif($tab==='cicilan') Belum ada santri dengan cicilan untuk periode ini.
                            @else Tidak ada tagihan yang belum dibayar untuk periode ini.
                            @endif
                        </span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- â”€â”€ Pagination â”€â”€ --}}
    @if($totalPages>1)
    <div style="margin-top:14px;display:flex;justify-content:center;align-items:center;gap:10px;">
        @if($currentPage>1)
            <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->all(),['page'=>$currentPage-1])) }}" class="btn btn-sm btn-secondary"><i class="fas fa-chevron-left"></i> Sebelumnya</a>
        @endif
        <span style="padding:6px 11px;background:#f8f9fa;border-radius:4px;font-weight:600;">Halaman {{ $currentPage }} dari {{ $totalPages }}</span>
        @if($currentPage<$totalPages)
            <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->all(),['page'=>$currentPage+1])) }}" class="btn btn-sm btn-secondary">Selanjutnya <i class="fas fa-chevron-right"></i></a>
        @endif
    </div>
    @endif
</div>

{{-- â”€â”€ Modal Catat Cicilan â”€â”€ --}}
<div id="modalCicilan" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.3);margin:16px;">
        <h4 style="margin:0 0 4px;"><i class="fas fa-coins" style="color:#9c27b0;"></i> Catat Cicilan</h4>
        <p id="modalNama" style="margin:0 0 14px;color:#666;font-size:.88rem;"></p>

        <div style="background:#f8f9fa;border-radius:8px;padding:11px;margin-bottom:14px;font-size:.83rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:3px;"><span>Total Tagihan</span><strong id="mTagihan"></strong></div>
            <div style="display:flex;justify-content:space-between;margin-bottom:3px;"><span>Sudah Dibayar</span><strong id="mTerbayar" style="color:#28a745;"></strong></div>
            <div style="display:flex;justify-content:space-between;"><span>Sisa</span><strong id="mSisa" style="color:#dc3545;"></strong></div>
        </div>

        <form id="formCicilan" method="POST">
            @csrf
            <div style="margin-bottom:10px;">
                <label style="font-weight:600;font-size:.84rem;display:block;margin-bottom:3px;">Nominal Cicilan Sekarang (Rp) *</label>
                <input type="number" name="nominal_cicilan" id="inputNominal" class="form-control" min="1" required placeholder="Contoh: 100000">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-weight:600;font-size:.84rem;display:block;margin-bottom:3px;">Catatan (opsional)</label>
                <input type="text" name="catatan" class="form-control" placeholder="Misal: Cicilan ke-1">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary" style="flex:1;"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="tutupModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<style>
/* KPI Cards */
.kpi-card { display:flex;align-items:center;justify-content:space-between;padding:13px;border-radius:8px;color:#fff;box-shadow:0 3px 8px rgba(0,0,0,.12); }
.kpi-label { font-size:11px;opacity:.9;margin-bottom:4px; }
.kpi-val   { font-size:20px;font-weight:bold; }
.kpi-sub   { font-size:11px;opacity:.8; }
.kpi-icon  { font-size:26px;opacity:.25; }
/* Tabs */

.spp-tab {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 14px; border-radius:8px 8px 0 0;
    font-weight:600; font-size:.85rem;
    border:1px solid #dee2e6; border-bottom:3px solid transparent;
    text-decoration:none; transition:all .2s;
    background:#f8f9fa; color:#6c757d;
}
.spp-tab:hover { background:#e9ecef; color:#495057; }
/* Belum Bayar: merah pastel */
.spp-tab-danger         { background:linear-gradient(135deg,#f8d7da,#f5c6cb); border-color:#f5c6cb; border-bottom-color:#721c24; color:#721c24; }
.spp-tab-outline-danger { color:#721c24; }
.spp-tab-outline-danger:hover { background:linear-gradient(135deg,#f8d7da,#f5c6cb); color:#721c24; }
/* Cicilan: ungu pastel */
.spp-tab-purple         { background:linear-gradient(135deg,#ede7f6,#d1c4e9); border-color:#d1c4e9; border-bottom-color:#4a148c; color:#4a148c; }
.spp-tab-outline-purple { color:#6a1b9a; }
.spp-tab-outline-purple:hover { background:linear-gradient(135deg,#ede7f6,#d1c4e9); color:#4a148c; }
/* Sudah Bayar: hijau pastel */
.spp-tab-success        { background:linear-gradient(135deg,#d4edda,#c3e6cb); border-color:#c3e6cb; border-bottom-color:#155724; color:#155724; }
.spp-tab-outline-success{ color:#155724; }
.spp-tab-outline-success:hover { background:linear-gradient(135deg,#d4edda,#c3e6cb); color:#155724; }
/* Badge counter */
.tab-badge { display:inline-block; padding:1px 7px; border-radius:10px; font-size:.72rem; font-weight:700; background:rgba(0,0,0,.12); color:inherit; }
/* Badges */
.badge { padding:4px 9px;border-radius:20px;font-weight:600;font-size:.75rem; }
.badge-success  { background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff; }
.badge-warning  { background:#ffc107;color:#333; }
.badge-danger   { background:linear-gradient(135deg,#ff6b6b,#ee5a6f);color:#fff;animation:pulse 2s infinite; }
.badge-secondary{ background:#6c757d;color:#fff; }
.badge-telat    { display:inline-block;background:#dc3545;color:#fff;padding:2px 7px;border-radius:10px;font-size:.72rem;font-weight:700; }
/* Progress bar */
.progress-bar-wrap { background:#e9ecef;border-radius:8px;height:9px;overflow:hidden;margin-bottom:2px; }
.progress-bar-fill { background:linear-gradient(90deg,#9c27b0,#e040fb);height:100%;transition:width .3s; }
/* Buttons */
.btn-purple { background:linear-gradient(135deg,#9c27b0,#e040fb);color:#fff;border:none; }
.btn-purple:hover { opacity:.88; }
/* Misc */
.tr-telat { background:#fff5f5; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:.7} }
.data-table tbody tr:hover { background:#f8f9fa; }
</style>

<script>
function fmt(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }
function bukaCatatCicilan(id, nama, tagihan, terbayar) {
    document.getElementById('modalNama').textContent  = nama;
    document.getElementById('mTagihan').textContent   = fmt(tagihan);
    document.getElementById('mTerbayar').textContent  = fmt(terbayar);
    document.getElementById('mSisa').textContent      = fmt(tagihan - terbayar);
    document.getElementById('inputNominal').max       = tagihan - terbayar;
    document.getElementById('formCicilan').action     = '/admin/pembayaran-spp/' + id + '/cicilan';
    var modal = document.getElementById('modalCicilan');
    modal.style.display = 'flex';
}
function tutupModal() {
    document.getElementById('modalCicilan').style.display = 'none';
}
document.getElementById('modalCicilan').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});
</script>
@endsection