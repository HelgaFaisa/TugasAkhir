{{-- resources/views/admin/mesin/import/preview.blade.php --}}
@extends('layouts.app')
@section('title', 'Preview Import Absensi')
@section('content')
@php
use Carbon\Carbon;

$statusStyle = [
    'Hadir'     => ['bg'=>'#DCFCE7','c'=>'#166534','ic'=>'✅'],
    'Terlambat' => ['bg'=>'#FEF9C3','c'=>'#92400E','ic'=>'⏰'],
    'Alpa'      => ['bg'=>'#FEE2E2','c'=>'#991B1B','ic'=>'❌'],
    'Pulang'    => ['bg'=>'#FFF7ED','c'=>'#9A3412','ic'=>'🏠'],
    'Izin'      => ['bg'=>'#F3E8FF','c'=>'#6B21A8','ic'=>'📋'],
    'Sakit'     => ['bg'=>'#E0F2FE','c'=>'#0C4A6E','ic'=>'🏥'],
];

// ── 1. Kolom kegiatan: UNIK, diurutkan waktu_mulai ───────────────────────────
$kegiatanCols = collect($hasilEnriched)
    ->flatMap(fn($h) => $h['rows'])
    ->unique('kegiatan_id')
    ->sortBy('waktu_mulai')
    ->values()
    ->map(fn($r) => [
        'kegiatan_id' => $r['kegiatan_id'],
        'nama'        => $r['nama_kegiatan'],
        'waktu_mulai' => $r['waktu_mulai'],
    ]);

// ── 2. Susun data: [tanggal][id_santri_or_mesin] = data ──────────────────────
$byTanggalSantri = [];
$santriList      = [];  // untuk urutan santri konsisten

foreach ($hasilEnriched as $h) {
    $tgl = $h['tanggal'];
    $key = $h['id_santri'] ?? ('__'.$h['id_mesin']);
    $byTanggalSantri[$tgl][$key] = $h;

    if (!isset($santriList[$key])) {
        $santriList[$key] = [
            'nama'   => $h['nama_web'] ?? $h['nama_mesin'],
            'kelas'  => $h['kelas'] ?? '-',
            'status' => $h['match_status'],
        ];
    }
}
ksort($byTanggalSantri);

// ── 3. Statistik ─────────────────────────────────────────────────────────────
$allRows      = collect($hasilEnriched)->flatMap(fn($h) => $h['rows']);
$totalKonflik = $allRows->where('is_conflict', true)->count();
$hadir        = $allRows->where('status_final','Hadir')->count();
$terlambat    = $allRows->where('status_final','Terlambat')->count();
$alpa         = $allRows->where('status_final','Alpa')->count();
$notMapped    = collect($hasilEnriched)->where('match_status','NOT_MAPPED')->count();
@endphp

<style>
/* Sticky top bar */
.top-bar {
    position: sticky; top: 0; z-index: 50;
    background: #0F172A; color: #F1F5F9;
    padding: 8px 16px; display: flex; align-items: center;
    gap: 10px; flex-wrap: wrap; box-shadow: 0 2px 8px rgba(0,0,0,.3);
    font-size: 13px;
}
.chip {
    border-radius: 8px; padding: 4px 10px;
    text-align: center; min-width: 56px;
    font-size: 11px; line-height: 1.3;
}
.chip .n { font-size: 17px; font-weight: 700; display: block }
.btn-act {
    border: none; border-radius: 6px; padding: 6px 12px;
    cursor: pointer; font-weight: 600; font-size: 12px;
    white-space: nowrap;
}
.btn-save {
    border: none; border-radius: 8px; padding: 8px 20px;
    cursor: pointer; font-weight: 700; font-size: 13px;
    color: #fff; white-space: nowrap; transition: background .2s;
}

/* Matrix table */
.wrap { overflow-x: auto }
.mx { border-collapse: collapse; font-size: 12px; width: 100% }
.mx th, .mx td {
    border: 1px solid #E5E7EB; padding: 0;
    white-space: nowrap;
}
/* Sticky kolom tanggal + nama */
.col-tgl {
    position: sticky; left: 0; z-index: 4;
    background: #F8FAFC; min-width: 90px;
    border-right: 2px solid #CBD5E1;
    padding: 6px 10px; font-size: 11px;
}
.col-nama {
    position: sticky; left: 90px; z-index: 4;
    background: #F8FAFC; min-width: 130px;
    border-right: 2px solid #CBD5E1;
    padding: 6px 10px;
}
/* Header kegiatan (rotate) */
.th-wrap {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    display: flex; align-items: center; justify-content: flex-end;
    gap: 2px; height: 80px; padding: 5px 6px;
}
/* Status pill */
.pill {
    display: inline-block; border-radius: 8px;
    padding: 2px 6px; font-size: 10px; font-weight: 700;
}
/* Konflik cell */
.conf-cell { background: #FFF5F5 !important; border: 2px solid #FCA5A5 !important }
.conf-wrap { display: flex; flex-direction: column }
.conf-opt {
    padding: 4px 8px; cursor: pointer;
    font-size: 11px; display: flex; align-items: center; gap: 4px;
    border-bottom: 1px solid #F1F5F9; transition: background .12s;
}
.conf-opt:last-child { border-bottom: none }
.conf-opt:hover { background: #F8FAFC }
.sel-m { background: #DCFCE7 !important }
.sel-e { background: #DBEAFE !important }
/* Date separator row */
.date-sep td {
    background: #1E293B; color: #94A3B8; font-weight: 700;
    font-size: 11px; padding: 5px 12px; border-bottom: 2px solid #334155;
}
/* Alternating santri rows */
.row-alt { background: #FAFAFA }
/* Sticky header */
.th-sticky {
    position: sticky; top: 52px; z-index: 6;
    background: #1E293B;
}
.th-tgl { position: sticky; left: 0; z-index: 8 }
.th-nama { position: sticky; left: 90px; z-index: 8 }
</style>

<form action="{{ route('admin.mesin.import.store') }}" method="POST" id="frm">
@csrf
<input type="hidden" name="conflict_strategy" value="manual" id="stratInput">

{{-- Error flash --}}
@if(session('error'))
<div style="background:#FEE2E2;border:1px solid #FCA5A5;border-left:4px solid #DC2626;
            padding:12px 16px;font-size:13px;color:#991B1B">
    <strong>❌ Error:</strong> {{ session('error') }}
</div>
@endif

{{-- ── TOP BAR ──────────────────────────────────────────────────────────────── --}}
<div class="top-bar">
    <div style="flex:1;min-width:160px">
        <div style="color:#64748B;font-size:10px;text-transform:uppercase;letter-spacing:1px">
            Preview Import
        </div>
        <div style="font-weight:700;font-size:14px;margin-top:1px">
            {{ count($santriList) }} santri
            @if($totalKonflik > 0)
                · <span style="color:#FCA5A5" id="lbl">
                    ⚡ <span id="cnt">{{ $totalKonflik }}</span> konflik perlu diselesaikan
                  </span>
            @else
                · <span style="color:#86EFAC">✅ Siap disimpan</span>
            @endif
        </div>
    </div>

    {{-- Stat chips --}}
    <div class="chip" style="background:#DCFCE7;color:#166534">
        <span class="n">{{ $hadir }}</span>Hadir
    </div>
    <div class="chip" style="background:#FEF9C3;color:#92400E">
        <span class="n">{{ $terlambat }}</span>Telat
    </div>
    <div class="chip" style="background:#FEE2E2;color:#991B1B">
        <span class="n">{{ $alpa }}</span>Alpa
    </div>
    @if($totalKonflik > 0)
    <div class="chip" style="background:#FEE2E2;color:#DC2626">
        <span class="n" id="chip">{{ $totalKonflik }}</span>Konflik
    </div>
    @endif
    @if($notMapped > 0)
    <div class="chip" style="background:#FFF3E8;color:#C05621">
        <span class="n">{{ $notMapped }}</span>Blm Map
    </div>
    @endif

    {{-- Conflict actions --}}
    @if($totalKonflik > 0)
    <div style="display:flex;flex-direction:column;gap:3px;font-size:11px;color:#94A3B8">
        Konflik:
    </div>
    <button type="button" class="btn-act" style="background:#DCFCE7;color:#166534"
            onclick="resolveAll('m');document.getElementById('stratInput').value='mesin'">👆 Mesin</button>
    <button type="button" class="btn-act" style="background:#DBEAFE;color:#1D4ED8"
            onclick="resolveAll('e');document.getElementById('stratInput').value='exist'">🔒 Lama</button>
    @endif

    {{-- Save --}}
    <button type="button" class="btn-save" id="saveBtn"
            style="background:{{ $totalKonflik > 0 ? '#64748B' : 'linear-gradient(135deg,#166534,#22C55E)' }}"
            {{ $totalKonflik > 0 ? 'disabled' : '' }}
            onclick="submitForm()">
        @if($totalKonflik > 0) ⏳ Selesaikan konflik dulu
        @else 💾 Simpan ke Database @endif
    </button>

    <a href="{{ route('admin.mesin.import.index') }}"
       class="btn-act" style="background:#374151;color:#F1F5F9;text-decoration:none">
        ← Kembali
    </a>
</div>

{{-- Legenda --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;padding:8px 16px;
            background:#F8FAFC;border-bottom:1px solid #E5E7EB;font-size:11px">
    <span style="color:#6B7280;font-weight:600">Status:</span>
    @foreach($statusStyle as $st => $s)
    <span class="pill" style="background:{{$s['bg']}};color:{{$s['c']}}">
        {{ $s['ic'] }} {{ $st }}
    </span>
    @endforeach
    <span style="color:#9CA3AF;margin-left:4px">| — = tidak ada data</span>
    <span style="border:2px solid #FCA5A5;border-radius:4px;padding:1px 6px;color:#991B1B">
        ⚡ Konflik
    </span>
    <span style="color:#9CA3AF">= ada data berbeda, pilih salah satu</span>
    @if($notMapped > 0)
    <span style="margin-left:auto">
        ⚠️ {{ $notMapped }} belum dipetakan →
        <a href="{{ route('admin.mesin.mapping-santri.index') }}" target="_blank">
            Lengkapi Mapping
        </a>
    </span>
    @endif
</div>

{{-- ── MATRIX TABLE ──────────────────────────────────────────────────────────── --}}
<div class="wrap">
<table class="mx">

{{-- Sticky header --}}
<thead>
<tr>
    {{-- Tanggal header --}}
    <th class="th-sticky th-tgl"
        style="min-width:90px;padding:8px 10px;text-align:left;
               color:#94A3B8;font-size:10px;border-right:2px solid #334155">
        Tanggal
    </th>
    {{-- Nama header --}}
    <th class="th-sticky th-nama"
        style="min-width:130px;padding:8px 10px;text-align:left;
               color:#94A3B8;font-size:10px;border-right:2px solid #334155">
        Santri
    </th>
    {{-- Kolom kegiatan — UNIK, diurutkan waktu --}}
    @foreach($kegiatanCols as $kg)
    <th class="th-sticky" style="min-width:70px;vertical-align:bottom">
        <div class="th-wrap">
            <span style="color:#F1F5F9;font-size:10px;font-weight:600">
                {{ $kg['nama'] }}
            </span>
            <span style="color:#64748B;font-size:9px">
                {{ $kg['waktu_mulai'] }}
            </span>
        </div>
    </th>
    @endforeach
</tr>
</thead>

{{-- Body: iterasi per tanggal, lalu per santri --}}
<tbody>
@foreach($byTanggalSantri as $tgl => $santriRows)

@php
    $tglCarbon = Carbon::parse($tgl);
    $tglLabel  = $tglCarbon->locale('id')->isoFormat('ddd, D MMM');
    $isOdd     = ($loop->index % 2 === 1);
@endphp

{{-- Setiap tanggal: satu baris per santri --}}
@foreach($santriList as $key => $info)
@php
    $data  = $santriRows[$key] ?? null;
    $rowBg = ($loop->index % 2 === 0) ? 'white' : '#FAFAFA';
    if ($data && $data['match_status'] === 'NOT_MAPPED') $rowBg = '#FFF5F5';
@endphp
<tr style="background:{{ $rowBg }}">

    {{-- Kolom Tanggal (hanya tampil di baris pertama per tanggal) --}}
    <td class="col-tgl" style="background:{{ $rowBg }}">
        @if($loop->first)
        <strong style="color:#1E293B">{{ $tglLabel }}</strong>
        @endif
    </td>

    {{-- Kolom Nama --}}
    <td class="col-nama" style="background:{{ $rowBg }}">
        @if($info['status'] === 'NOT_MAPPED')
            <div style="font-size:10px;font-weight:700;color:#DC2626">⚠ BELUM MAP</div>
            <div style="font-size:10px;color:#9CA3AF">{{ $info['nama'] }}</div>
        @else
            <div style="font-weight:600;color:#1F2937;font-size:12px">
                {{ $info['nama'] }}
            </div>
            <div style="font-size:10px;color:#9CA3AF">{{ $info['kelas'] }}</div>
        @endif
    </td>

    {{-- Kolom per kegiatan --}}
    @foreach($kegiatanCols as $kg)
    @php
        $row = $data
            ? collect($data['rows'])->firstWhere('kegiatan_id', $kg['kegiatan_id'])
            : null;
        $sf  = $row['status_final'] ?? null;
        $st  = $sf ? ($statusStyle[$sf] ?? null) : null;
        $isConf = $row['is_conflict'] ?? false;
        $key2   = "{$kg['kegiatan_id']}_{$data['id_santri']}_{$tgl}";
    @endphp

    <td style="padding:0;text-align:center;vertical-align:middle;min-width:70px"
        class="{{ $isConf ? 'conf-cell' : '' }}">

        @if(!$data || !$row || $sf === null)
            {{-- Tidak ada data --}}
            <span style="color:#D1D5DB">—</span>

        @elseif($isConf)
            {{-- ── KONFLIK: 2 pilihan ── --}}
            @php
                $ex   = $row['existing'];
                $exSt = $statusStyle[$ex['status']] ?? ['bg'=>'#F9FAFB','c'=>'#6B7280','ic'=>'?'];
            @endphp
            <div class="conf-wrap" data-key="{{ $key2 }}">
                {{-- Pilihan mesin --}}
                <div class="conf-opt" data-ch="m" onclick="pick('{{ $key2 }}','m',this)">
                    <input type="radio" name="conflict_choices[{{ $key2 }}]"
                           value="mesin" id="cm_{{ $key2 }}" style="display:none">
                    <span>👆</span>
                    <div>
                        <span class="pill" style="background:{{$st['bg']}};color:{{$st['c']}}">
                            {{$st['ic']}} {{$sf}}
                        </span>
                        <div style="font-size:9px;color:#6B7280">
                            Mesin·{{ $row['jam_scan'] ?? '-' }}
                        </div>
                    </div>
                </div>
                {{-- Pilihan lama --}}
                <div class="conf-opt" data-ch="e" onclick="pick('{{ $key2 }}','e',this)">
                    <input type="radio" name="conflict_choices[{{ $key2 }}]"
                           value="exist" id="ce_{{ $key2 }}" style="display:none">
                    <span>🔒</span>
                    <div>
                        <span class="pill" style="background:{{$exSt['bg']}};color:{{$exSt['c']}}">
                            {{$exSt['ic']}} {{$ex['status']}}
                        </span>
                        <div style="font-size:9px;color:#6B7280">
                            {{ $ex['metode'] ?? 'Manual' }}
                            @if($ex['waktu']) ·{{ substr($ex['waktu'],0,5) }}@endif
                        </div>
                    </div>
                </div>
                <div style="background:#FEE2E2;padding:1px 4px;font-size:9px;
                            color:#DC2626;font-weight:700;text-align:center">
                    ⚡ pilih
                </div>
            </div>

        @else
            {{-- ── Normal ── --}}
            <div style="padding:5px 3px">
                <span class="pill" style="background:{{$st['bg']}};color:{{$st['c']}}">
                    {{$st['ic']}} {{$sf}}
                </span>
                @if($row['jam_scan'])
                <div style="font-size:9px;color:#9CA3AF;margin-top:1px">
                    {{ $row['jam_scan'] }}
                    @if(($row['selisih_menit'] ?? 0) > 0)
                    <span style="color:#F59E0B">+{{ $row['selisih_menit'] }}m</span>
                    @endif
                </div>
                @endif
            </div>
        @endif
    </td>
    @endforeach
</tr>
@endforeach

@endforeach
</tbody>

{{-- Repeat header di bawah untuk tabel panjang --}}
<tfoot>
<tr>
    <th style="background:#1E293B;color:#94A3B8;font-size:10px;
               padding:6px 10px;border-right:2px solid #334155;
               position:sticky;left:0">Tanggal</th>
    <th style="background:#1E293B;color:#94A3B8;font-size:10px;
               padding:6px 10px;border-right:2px solid #334155;
               position:sticky;left:90px">Santri</th>
    @foreach($kegiatanCols as $kg)
    <th style="background:#1E293B;color:#94A3B8;font-size:9px;padding:4px 6px">
        {{ $kg['nama'] }}
    </th>
    @endforeach
</tr>
</tfoot>
</table>
</div>

{{-- Footer stats --}}
<div style="padding:8px 16px;background:#F8FAFC;border-top:1px solid #E5E7EB;
            font-size:11px;color:#6B7280;display:flex;gap:12px;flex-wrap:wrap">
    <span>📊 {{ count($santriList) }} santri · {{ count($byTanggalSantri) }} hari</span>
    <span>✅ {{ $hadir }} Hadir</span>
    <span>⏰ {{ $terlambat }} Terlambat</span>
    <span>❌ {{ $alpa }} Alpa</span>
    @if($totalKonflik > 0)
    <span style="color:#DC2626">⚡ {{ $totalKonflik }} Konflik</span>
    @endif
    <span style="margin-left:auto">
        Toleransi: {{ $tolSebelum }}m sebelum / {{ $tolSesudah }}m sesudah
    </span>
</div>

</form>

<script>
const totalK = {{ $totalKonflik }};
const done   = new Set();

function pick(key, ch, el) {
    const wrap = el.closest('.conf-wrap');
    wrap.querySelectorAll('.conf-opt').forEach(o => {
        o.classList.remove('sel-m','sel-e');
    });
    el.classList.add(ch === 'm' ? 'sel-m' : 'sel-e');
    const radio = document.getElementById((ch==='m'?'cm_':'ce_') + key);
    if (radio) radio.checked = true;
    done.add(key);
    updateUI();
}

function resolveAll(ch) {
    document.querySelectorAll('.conf-wrap').forEach(wrap => {
        const key = wrap.dataset.key;
        const opt = wrap.querySelector('[data-ch="'+ch+'"]');
        if (opt) pick(key, ch, opt);
    });
}

function submitForm() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.style.background = '#64748B';
    btn.textContent = '⏳ Menyimpan...';
    document.getElementById('frm').submit();
}

function updateUI() {
    const rem  = totalK - done.size;
    const btn  = document.getElementById('saveBtn');
    const lbl  = document.getElementById('lbl');
    const chip = document.getElementById('chip');
    if (rem <= 0) {
        btn.disabled = false;
        btn.style.background = 'linear-gradient(135deg,#166534,#22C55E)';
        btn.textContent = '💾 Simpan ke Database';
        if (lbl)  lbl.innerHTML = '<span style="color:#86EFAC">✅ Semua konflik selesai</span>';
        if (chip) chip.textContent = '0';
    } else {
        btn.disabled = true;
        btn.style.background = '#64748B';
        btn.textContent = '⏳ Selesaikan ' + rem + ' konflik';
        if (lbl)  lbl.innerHTML = '<span style="color:#FCA5A5">⚡ <span id="cnt">'+rem+'</span> konflik</span>';
        if (chip) chip.textContent = rem;
    }
}
</script>

@endsection