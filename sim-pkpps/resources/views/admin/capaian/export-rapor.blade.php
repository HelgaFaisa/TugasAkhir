<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Capaian - {{ $santri->nama_lengkap }} - {{ $semester->nama_semester }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: #fff; padding: 20px; font-size: 11pt; }

        .rapor-header { text-align: center; padding-bottom: 20px; border-bottom: 3px double #6FBA9D; margin-bottom: 24px; }
        .rapor-header h1 { font-size: 16pt; color: #2e7d32; margin-bottom: 4px; }
        .rapor-header h2 { font-size: 12pt; color: #555; font-weight: 400; margin-bottom: 8px; }
        .rapor-header .subtitle { font-size: 10pt; color: #888; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; margin-bottom: 24px; background: #f8faf9; border-radius: 8px; padding: 16px; border: 1px solid #e0e0e0; }
        .info-item { padding: 4px 0; font-size: 10pt; }
        .info-item .label { color: #888; display: inline-block; width: 130px; }
        .info-item .value { font-weight: 600; color: #333; }

        .summary-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .summary-box { text-align: center; padding: 14px 10px; border-radius: 8px; border: 1px solid #e0e0e0; }
        .summary-box .sb-val { font-size: 18pt; font-weight: 800; }
        .summary-box .sb-label { font-size: 8pt; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }
        .sb-green { border-color: #c8e6c9; background: #f1f8e9; }
        .sb-green .sb-val { color: #2e7d32; }
        .sb-blue { border-color: #bbdefb; background: #e3f2fd; }
        .sb-blue .sb-val { color: #1565c0; }
        .sb-amber { border-color: #ffecb3; background: #fffde7; }
        .sb-amber .sb-val { color: #f57f17; }
        .sb-red { border-color: #ffcdd2; background: #fbe9e7; }
        .sb-red .sb-val { color: #c62828; }

        h3 { font-size: 12pt; color: #2e7d32; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #e8f5e9; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 9.5pt; }
        th { background: #e8f5e9; color: #2e7d32; font-weight: 700; padding: 8px 6px; text-align: left; border: 1px solid #c8e6c9; font-size: 8.5pt; text-transform: uppercase; letter-spacing: 0.3px; }
        td { padding: 7px 6px; border: 1px solid #e0e0e0; }
        tbody tr:nth-child(even) { background: #fafafa; }
        tbody tr:hover { background: #f1f8e9; }

        .progress-cell { width: 100px; }
        .prog-bar-mini { height: 8px; background: #e8e8e8; border-radius: 4px; overflow: hidden; }
        .prog-fill-mini { height: 100%; border-radius: 4px; }

        .badge-sm { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: 600; }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .badge-warning { background: #fff8e1; color: #f57f17; }
        .badge-danger { background: #fbe9e7; color: #c62828; }
        .badge-info { background: #e3f2fd; color: #1565c0; }

        .comparison { font-size: 8.5pt; margin-top: 2px; }
        .comp-up { color: #2e7d32; } .comp-down { color: #c62828; } .comp-same { color: #999; }

        .kategori-section { margin-bottom: 20px; }
        .kategori-header { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 6px; margin-bottom: 8px; }
        .kat-alquran { background: linear-gradient(90deg, #e8f5e9, #f1f8e9); }
        .kat-hadist { background: linear-gradient(90deg, #e3f2fd, #e8f4fd); }
        .kat-tambahan { background: linear-gradient(90deg, #fffde7, #fff8e1); }

        .catatan-box { background: #f5f8f6; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .catatan-box h4 { font-size: 10pt; color: #555; margin-bottom: 8px; }
        .catatan-lines { min-height: 60px; }
        .catatan-line { border-bottom: 1px dotted #ccc; height: 24px; }

        .footer { text-align: center; padding-top: 20px; border-top: 2px solid #e0e0e0; margin-top: 30px; font-size: 9pt; color: #999; }

        .print-btn { position: fixed; bottom: 20px; right: 20px; background: #6FBA9D; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-size: 11pt; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(111,186,157,0.4); z-index: 999; }
        .print-btn:hover { background: #5EA98C; }

        @media print {
            body { padding: 10mm; }
            .print-btn { display: none !important; }
            .no-print { display: none !important; }
            @page { margin: 10mm; size: A4; }
        }
    </style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">
    <i>🖨️</i> Cetak / Simpan PDF
</button>

{{-- HEADER --}}
<div class="rapor-header">
    <h1>RAPOR CAPAIAN AL-QUR'AN & HADIST</h1>
    <h2>Pondok Pesantren PKPPS</h2>
    <div class="subtitle">{{ $semester->nama_semester }} — Tahun Ajaran {{ $semester->tahun_ajaran }}</div>
</div>

{{-- INFO SANTRI --}}
<div class="info-grid">
    <div class="info-item"><span class="label">Nama Lengkap</span> <span class="value">{{ $santri->nama_lengkap }}</span></div>
    <div class="info-item"><span class="label">NIS</span> <span class="value">{{ $santri->nis }}</span></div>
    <div class="info-item"><span class="label">Kelas</span> <span class="value">{{ $santri->kelas }}</span></div>
    <div class="info-item"><span class="label">Status</span> <span class="value">{{ $santri->status }}</span></div>
    <div class="info-item"><span class="label">Semester</span> <span class="value">{{ $semester->nama_semester }}</span></div>
    <div class="info-item"><span class="label">Tanggal Cetak</span> <span class="value">{{ now()->format('d F Y') }}</span></div>
</div>

{{-- SUMMARY --}}
<div class="summary-row">
    <div class="summary-box sb-green">
        <div class="sb-val">{{ number_format($avgProgress, 1) }}%</div>
        <div class="sb-label">Rata-rata Progress</div>
        @if($prevSemester)
            <div class="comparison {{ $avgProgress >= $avgPrev ? 'comp-up' : 'comp-down' }}">
                {{ $avgProgress >= $avgPrev ? '▲' : '▼' }} {{ number_format(abs($avgProgress - $avgPrev), 1) }}% dari {{ $prevSemester->nama_semester }}
            </div>
        @endif
    </div>
    <div class="summary-box sb-blue">
        <div class="sb-val">{{ $totalMateri }}</div>
        <div class="sb-label">Total Materi</div>
    </div>
    <div class="summary-box sb-amber">
        <div class="sb-val">{{ $selesai }}</div>
        <div class="sb-label">Materi Selesai</div>
    </div>
    <div class="summary-box {{ $avgProgress >= 70 ? 'sb-green' : ($avgProgress >= 40 ? 'sb-amber' : 'sb-red') }}">
        <div class="sb-val">{{ $avgProgress >= 80 ? 'A' : ($avgProgress >= 65 ? 'B' : ($avgProgress >= 50 ? 'C' : 'D')) }}</div>
        <div class="sb-label">Predikat</div>
    </div>
</div>

{{-- PROGRESS PER KATEGORI --}}
<h3>Ringkasan Per Kategori</h3>
<table>
    <thead>
        <tr>
            <th>Kategori</th>
            <th style="text-align:center;">Jumlah Materi</th>
            <th style="text-align:center;">Selesai</th>
            <th style="text-align:center;">Rata-rata Progress</th>
            <th style="text-align:center;">Semester Lalu</th>
            <th style="text-align:center;">Perubahan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($perKategori as $kat => $data)
        <tr>
            <td><strong>{{ $kat }}</strong></td>
            <td style="text-align:center;">{{ $data['count'] }}</td>
            <td style="text-align:center;">{{ $data['selesai'] }}</td>
            <td style="text-align:center;">
                <span class="badge-sm {{ $data['avg'] >= 70 ? 'badge-success' : ($data['avg'] >= 40 ? 'badge-warning' : 'badge-danger') }}">
                    {{ number_format($data['avg'], 1) }}%
                </span>
            </td>
            <td style="text-align:center;">{{ number_format($data['prev'], 1) }}%</td>
            <td style="text-align:center;">
                @php $change = $data['avg'] - $data['prev']; @endphp
                <span class="{{ $change > 0 ? 'comp-up' : ($change < 0 ? 'comp-down' : 'comp-same') }}">
                    {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- DETAIL PER MATERI --}}
<h3>Detail Progress Per Materi</h3>
<table>
    <thead>
        <tr>
            <th style="width:5%;">No</th>
            <th style="width:25%;">Nama Materi</th>
            <th style="width:12%;">Kategori</th>
            <th style="width:10%;">Halaman</th>
            <th class="progress-cell" style="width:15%;">Progress</th>
            <th style="width:10%;">Persentase</th>
            <th style="width:10%;">Sem. Lalu</th>
            <th style="width:13%;">Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($capaians as $idx => $cap)
        @php
            $prevCap = $prevCapaians->where('id_materi', $cap->id_materi)->first();
            $prevPct = $prevCap ? floatval($prevCap->persentase) : 0;
            $changePct = floatval($cap->persentase) - $prevPct;
        @endphp
        <tr>
            <td style="text-align:center;">{{ $idx + 1 }}</td>
            <td><strong>{{ $cap->materi->nama_kitab }}</strong></td>
            <td>
                <span class="badge-sm {{ $cap->materi->kategori == 'Al-Qur\'an' ? 'badge-success' : ($cap->materi->kategori == 'Hadist' ? 'badge-info' : 'badge-warning') }}">
                    {{ $cap->materi->kategori }}
                </span>
            </td>
            <td style="font-size:8.5pt;">{{ $cap->halaman_selesai ?: '-' }}</td>
            <td>
                <div class="prog-bar-mini">
                    <div class="prog-fill-mini" style="width:{{ min($cap->persentase, 100) }}%;background:{{ $cap->persentase >= 80 ? '#66bb6a' : ($cap->persentase >= 50 ? '#ffa726' : '#ef5350') }};"></div>
                </div>
            </td>
            <td style="text-align:center;">
                <strong style="color:{{ $cap->persentase >= 100 ? '#2e7d32' : ($cap->persentase >= 50 ? '#f57f17' : '#c62828') }};">
                    {{ number_format($cap->persentase, 1) }}%
                </strong>
            </td>
            <td style="text-align:center;">
                {{ number_format($prevPct, 1) }}%
                <div class="{{ $changePct > 0 ? 'comp-up' : ($changePct < 0 ? 'comp-down' : 'comp-same') }}" style="font-size:8pt;">
                    {{ $changePct > 0 ? '+' : '' }}{{ number_format($changePct, 1) }}%
                </div>
            </td>
            <td style="font-size:8pt;">{{ $cap->catatan ?: '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;color:#999;padding:20px;">Belum ada data capaian untuk semester ini</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- CATATAN & REKOMENDASI --}}
<div class="catatan-box">
    <h4>Catatan / Rekomendasi Ustadz:</h4>
    <div class="catatan-lines">
        <div class="catatan-line"></div>
        <div class="catatan-line"></div>
        <div class="catatan-line"></div>
    </div>
</div>

{{-- TARGET SEMESTER DEPAN --}}
<div class="catatan-box">
    <h4>Target Semester Depan:</h4>
    <div class="catatan-lines">
        <div class="catatan-line"></div>
        <div class="catatan-line"></div>
    </div>
</div>

{{-- TANDA TANGAN --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:30px;">
    <div style="text-align:center;">
        <div style="font-size:9pt;color:#555;">Mengetahui,</div>
        <div style="font-size:10pt;font-weight:600;margin-top:4px;">Pimpinan Pondok</div>
        <div style="height:60px;"></div>
        <div style="border-top:1px solid #333;display:inline-block;padding-top:4px;min-width:180px;font-size:9.5pt;">
            (.................................)
        </div>
    </div>
    <div style="text-align:center;">
        <div style="font-size:9pt;color:#555;">{{ now()->format('d F Y') }}</div>
        <div style="font-size:10pt;font-weight:600;margin-top:4px;">Ustadz Pengampu</div>
        <div style="height:60px;"></div>
        <div style="border-top:1px solid #333;display:inline-block;padding-top:4px;min-width:180px;font-size:9.5pt;">
            (.................................)
        </div>
    </div>
</div>

{{-- FOOTER --}}
<div class="footer">
    Rapor ini dicetak secara otomatis oleh Sistem Informasi Manajemen PKPPS pada {{ now()->format('d F Y H:i') }}
</div>

</body>
</html>
