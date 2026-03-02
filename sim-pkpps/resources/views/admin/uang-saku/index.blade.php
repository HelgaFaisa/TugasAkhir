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

{{-- ── FILTER + KPI ── --}}
<div class="content-box" style="margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.uang-saku.index') }}" id="filterForm"
          style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; margin-bottom:18px;">
        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
        @if(request('sort'))   <input type="hidden" name="sort"   value="{{ request('sort') }}">   @endif

        <div>
            <label style="font-size:.78rem;color:var(--text-light);display:block;margin-bottom:3px;">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ $dari }}" style="width:155px;">
        </div>
        <div>
            <label style="font-size:.78rem;color:var(--text-light);display:block;margin-bottom:3px;">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ $sampai }}" style="width:155px;">
        </div>

        {{-- Preset cepat --}}
        <div style="display:flex;gap:5px;flex-wrap:wrap;align-self:flex-end;">
            @php
                $bulanIniDari   = now()->startOfMonth()->format('Y-m-d');
                $bulanIniSampai = now()->endOfMonth()->format('Y-m-d');
                $isBulanIni     = $dari === $bulanIniDari && $sampai === $bulanIniSampai;
                $isHariIni      = $dari === now()->format('Y-m-d') && $sampai === now()->format('Y-m-d');
            @endphp
            <button type="button" onclick="setPreset('today')"
                    class="btn btn-sm {{ $isHariIni ? 'btn-primary' : 'btn-secondary' }}">
                Hari Ini
            </button>
            <button type="button" onclick="setPreset('month')"
                    class="btn btn-sm {{ $isBulanIni ? 'btn-primary' : 'btn-secondary' }}">
                Bulan Ini
            </button>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Terapkan
            </button>
        </div>
    </form>

    {{--
        ┌─────────────────────────────────────────────────────────────────────┐
        │  KPI Cards                                                          │
        │                                                                     │
        │  Baris 1 (dipengaruhi filter tanggal):                              │
        │    • Total Transaksi  — jumlah baris transaksi di periode ini       │
        │    • Total Pemasukan  — uang masuk ke santri di periode ini         │
        │    • Total Pengeluaran — uang keluar dari santri di periode ini     │
        │    • Selisih (Net)    — pemasukan minus pengeluaran di periode ini  │
        │                         positif = lebih banyak uang masuk (surplus) │
        │                         negatif = lebih banyak uang keluar (defisit)│
        │                                                                     │
        │  Baris 2 (REAL-TIME, tidak terpengaruh filter):                     │
        │    • Total Saldo Seluruh Santri — total uang yang sedang dipegang   │
        │                         oleh seluruh santri aktif saat ini          │
        └─────────────────────────────────────────────────────────────────────┘
    --}}

    {{-- KPI Baris 1: berdasarkan filter periode --}}
    <div class="row-cards row-cards-4" style="margin-bottom:10px;">
        <div class="card card-info">
            <h3>Total Transaksi</h3>
            <p class="card-value">{{ $kpi['total_transaksi'] }}</p>
            <span class="card-sub">dari {{ $kpi['total_santri'] }} santri</span>
            <i class="fas fa-exchange-alt card-icon"></i>
        </div>
        <div class="card card-success">
            <h3>Total Pemasukan</h3>
            <p class="card-value" style="font-size:1.05rem;">Rp {{ number_format($kpi['total_pemasukan'], 0, ',', '.') }}</p>
            <span class="card-sub">
                {{ \Carbon\Carbon::parse($dari)->format('d M') }} &ndash; {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}
            </span>
            <i class="fas fa-arrow-circle-down card-icon"></i>
        </div>
        <div class="card card-warning">
            <h3>Total Pengeluaran</h3>
            <p class="card-value" style="font-size:1.05rem;">Rp {{ number_format($kpi['total_pengeluaran'], 0, ',', '.') }}</p>
            <span class="card-sub">
                {{ \Carbon\Carbon::parse($dari)->format('d M') }} &ndash; {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}
            </span>
            <i class="fas fa-arrow-circle-up card-icon"></i>
        </div>
        <div class="card {{ $kpi['selisih'] >= 0 ? 'card-success' : 'card-danger' }}">
            <h3>
                Selisih Periode
                <span title="Selisih = Total Pemasukan dikurangi Total Pengeluaran pada periode yang dipilih. Surplus berarti lebih banyak uang masuk; Defisit berarti lebih banyak uang keluar."
                      style="cursor:help;font-size:.75rem;color:var(--text-light);">
                    <i class="fas fa-question-circle"></i>
                </span>
            </h3>
            <p class="card-value" style="font-size:1.05rem;">
                {{ $kpi['selisih'] >= 0 ? '+' : '-' }} Rp {{ number_format(abs($kpi['selisih']), 0, ',', '.') }}
            </p>
            <span class="card-sub">{{ $kpi['selisih'] >= 0 ? '✓ Surplus periode ini' : '✗ Defisit periode ini' }}</span>
            <i class="fas fa-balance-scale card-icon"></i>
        </div>
    </div>

    {{-- KPI Baris 2: real-time, tidak berubah meski filter diganti --}}
    <div class="row-cards row-cards-1">
        <div class="card card-primary" style="border-left: 4px solid var(--primary-color); position:relative;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:space-between;">
                <div>
                    <h3 style="margin:0 0 4px;">
                        Total Saldo Seluruh Santri
                    </h3>
                    <p class="card-value" style="font-size:1.4rem;margin:0;color:var(--primary-color);">
                        Rp {{ number_format($kpi['total_saldo_realtime'], 0, ',', '.') }}
                    </p>
                </div>
                <div style="text-align:right;">
                    <span class="badge badge-info" style="font-size:.8rem;padding:5px 10px;">
                        <i class="fas fa-clock"></i> Real-time — tidak terpengaruh filter tanggal
                    </span>
                </div>
            </div>
            <i class="fas fa-piggy-bank card-icon"></i>
        </div>
    </div>
</div>

{{-- ── DAFTAR SANTRI ── --}}
<div class="content-box">

    {{-- Toolbar: Tambah + Search + Sort --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
        <a href="{{ route('admin.uang-saku.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </a>

        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            {{-- Sort --}}
            <form method="GET" action="{{ route('admin.uang-saku.index') }}" id="sortForm" style="display:flex;gap:6px;align-items:center;">
                <input type="hidden" name="dari"   value="{{ $dari }}">
                <input type="hidden" name="sampai" value="{{ $sampai }}">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                <label style="font-size:.79rem;color:var(--text-light);white-space:nowrap;">
                    <i class="fas fa-sort"></i> Urut:
                </label>
                <select name="sort" class="form-control form-control-sm" onchange="this.form.submit()" style="width:auto;">
                    <option value="nama"           {{ $sort==='nama'          ? 'selected' : '' }}>Nama A–Z</option>
                    <option value="saldo_asc"      {{ $sort==='saldo_asc'     ? 'selected' : '' }}>Saldo Terendah</option>
                    <option value="saldo_desc"     {{ $sort==='saldo_desc'    ? 'selected' : '' }}>Saldo Tertinggi</option>
                    <option value="transaksi_desc" {{ $sort==='transaksi_desc'? 'selected' : '' }}>Transaksi Terbanyak</option>
                    <option value="terakhir"       {{ $sort==='terakhir'      ? 'selected' : '' }}>Transaksi Terbaru</option>
                </select>
            </form>

            {{-- Search --}}
            <form method="GET" action="{{ route('admin.uang-saku.index') }}" style="display:flex;gap:6px;">
                <input type="hidden" name="dari"   value="{{ $dari }}">
                <input type="hidden" name="sampai" value="{{ $sampai }}">
                <input type="hidden" name="sort"   value="{{ $sort }}">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / ID santri..."
                       value="{{ request('search') }}" style="width:210px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('admin.uang-saku.index', ['dari'=>$dari,'sampai'=>$sampai,'sort'=>$sort]) }}"
                       class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                @endif
            </form>
        </div>
    </div>

    {{-- Legend saldo --}}
    <div style="display:flex;gap:14px;margin-bottom:12px;flex-wrap:wrap;font-size:.78rem;color:var(--text-light);">
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#6FBA9D;margin-right:4px;"></span>Saldo ≥ Rp 100rb</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f5a623;margin-right:4px;"></span>Saldo Rp 20rb – 99rb</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#FF8B94;margin-right:4px;"></span>Saldo &lt; Rp 20rb</span>
    </div>

    {{-- List santri --}}
    @if($santriList->count() > 0)
        @foreach($santriList as $santri)
        @php
            $saldoColor = $santri->saldo_terakhir >= 100000 ? '#6FBA9D'
                : ($santri->saldo_terakhir >= 20000 ? '#f5a623' : '#FF8B94');
            $saldoDot   = $saldoColor;
        @endphp
        <div class="content-box us-row" style="margin-bottom:10px;padding:0;overflow:hidden;">

            {{-- Baris utama — klik untuk expand --}}
            <div class="us-row-header"
                 onclick="toggleDetail('detail-{{ $santri->id_santri }}', this)"
                 style="display:flex;align-items:center;gap:0;cursor:pointer;padding:13px 16px;flex-wrap:wrap;gap:10px;">

                {{-- Chevron + Nama --}}
                <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:160px;">
                    <i class="fas fa-chevron-right toggle-arrow"
                       style="transition:transform .2s;color:var(--text-light);font-size:.8rem;flex-shrink:0;"></i>
                    <div>
                        <div style="font-weight:700;font-size:.93rem;">{{ $santri->nama_lengkap }}</div>
                        <div style="font-size:.76rem;color:var(--text-light);">{{ $santri->id_santri }}</div>
                    </div>
                </div>

                {{-- Saldo sekarang (prominent) --}}
                <div style="display:flex;flex-direction:column;align-items:center;min-width:120px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Saldo</div>
                    <div style="font-size:1.05rem;font-weight:800;color:{{ $saldoColor }};display:flex;align-items:center;gap:5px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $saldoDot }};flex-shrink:0;display:inline-block;"></span>
                        Rp {{ number_format($santri->saldo_terakhir, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Divider --}}
                <div style="width:1px;height:36px;background:var(--primary-light);flex-shrink:0;"></div>

                {{-- Pemasukan bulan ini --}}
                <div style="display:flex;flex-direction:column;align-items:center;min-width:100px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Masuk Bln Ini</div>
                    <div style="font-size:.85rem;font-weight:700;color:#6FBA9D;">
                        + Rp {{ number_format($santri->pemasukan_bulan, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Pengeluaran bulan ini --}}
                <div style="display:flex;flex-direction:column;align-items:center;min-width:100px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Keluar Bln Ini</div>
                    <div style="font-size:.85rem;font-weight:700;color:#FF8B94;">
                        - Rp {{ number_format($santri->pengeluaran_bulan, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Divider --}}
                <div style="width:1px;height:36px;background:var(--primary-light);flex-shrink:0;"></div>

                {{-- Transaksi + tanggal terakhir --}}
                <div style="display:flex;flex-direction:column;align-items:center;min-width:90px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Transaksi</div>
                    <span class="badge badge-info" style="font-size:.74rem;">{{ $santri->transaksi_bulan_ini }}x bln ini</span>
                    @if($santri->transaksi_terakhir_tgl)
                        <div style="font-size:.7rem;color:var(--text-light);margin-top:3px;">
                            terakhir {{ \Carbon\Carbon::parse($santri->transaksi_terakhir_tgl)->format('d/m/Y') }}
                        </div>
                    @endif
                </div>

                {{-- Aksi --}}
                <div style="display:flex;gap:5px;flex-shrink:0;" onclick="event.stopPropagation()">
                    <a href="{{ route('admin.uang-saku.create') }}?id_santri={{ $santri->id_santri }}"
                       class="btn btn-success btn-sm" title="Tambah Transaksi">
                        <i class="fas fa-plus"></i>
                    </a>
                    <a href="{{ route('admin.uang-saku.riwayat', $santri->id_santri) }}"
                       class="btn btn-primary btn-sm" title="Riwayat Lengkap">
                        <i class="fas fa-history"></i>
                    </a>
                </div>
            </div>

            {{-- Detail transaksi (collapsed) --}}
            <div id="detail-{{ $santri->id_santri }}"
                 style="display:none;border-top:1px solid var(--primary-light);padding:12px 16px;">
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
                                <td style="font-weight:600;color:{{ $tx->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94' }};">
                                    Rp {{ number_format($tx->saldo_sesudah, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div style="display:flex;gap:4px;justify-content:center;">
                                        <a href="{{ route('admin.uang-saku.show', $tx->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.uang-saku.edit', $tx->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.uang-saku.destroy', $tx->id) }}" method="POST"
                                              style="display:inline;" onsubmit="return confirm('Yakin hapus transaksi ini?')">
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
                        <div style="text-align:center;margin-top:10px;">
                            <a href="{{ route('admin.uang-saku.riwayat', $santri->id_santri) }}"
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-right"></i> Lihat Semua Riwayat
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-muted" style="margin:0;">Belum ada transaksi.</p>
                @endif
            </div>
        </div>
        @endforeach

        <div style="margin-top:14px;">{{ $santriList->links() }}</div>
    @else
        <div class="empty-state">
            <i class="fas fa-wallet"></i>
            <h3>Belum Ada Data</h3>
            <p>Belum ada santri dengan transaksi uang saku.</p>
            <a href="{{ route('admin.uang-saku.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    @endif
</div>

<script>
function toggleDetail(id, el) {
    var detail = document.getElementById(id);
    var arrow  = el.querySelector('.toggle-arrow');
    var open   = detail.style.display !== 'none';
    detail.style.display = open ? 'none' : 'block';
    arrow.style.transform = open ? 'rotate(0deg)' : 'rotate(90deg)';
}

function setPreset(type) {
    var form   = document.getElementById('filterForm');
    var dari   = form.querySelector('[name=dari]');
    var sampai = form.querySelector('[name=sampai]');
    var today  = new Date();
    var pad    = n => String(n).padStart(2, '0');
    var ymd    = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

    if (type === 'today') {
        dari.value   = ymd(today);
        sampai.value = ymd(today);
    } else if (type === 'month') {
        var first = new Date(today.getFullYear(), today.getMonth(), 1);
        var last  = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        dari.value   = ymd(first);
        sampai.value = ymd(last);
    }
    form.submit();
}
</script>
@endsection