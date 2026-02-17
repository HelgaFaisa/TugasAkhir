<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kegiatan - {{ $periodeLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px solid #10B981; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #10B981; margin-bottom: 4px; }
        .header h2 { font-size: 14px; color: #333; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 13px; font-weight: bold; color: #10B981; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px; }
        .kpi-row { display: table; width: 100%; margin-bottom: 16px; }
        .kpi-box { display: table-cell; width: 25%; padding: 8px; text-align: center; }
        .kpi-box .inner { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; }
        .kpi-box .label { font-size: 9px; color: #666; text-transform: uppercase; }
        .kpi-box .value { font-size: 20px; font-weight: bold; color: #333; }
        .kpi-box .sub { font-size: 9px; color: #999; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #f8fafb; color: #333; font-weight: 600; text-align: left; padding: 8px 6px; border: 1px solid #e2e8f0; }
        td { padding: 6px; border: 1px solid #e2e8f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-success { background: #ECFDF5; color: #065F46; }
        .badge-danger { background: #FEF2F2; color: #991B1B; }
        .badge-warning { background: #FFFBEB; color: #92400E; }
        .badge-info { background: #EFF6FF; color: #1E40AF; }
        .progress-bar { background: #e9ecef; border-radius: 4px; height: 10px; overflow: hidden; display: inline-block; width: 60px; vertical-align: middle; }
        .progress-fill { height: 100%; border-radius: 4px; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #999; display: flex; justify-content: space-between; }
        .page-break { page-break-after: always; }
        .highlight-row { background: #FEF2F2; }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <h1>SIM-PKPPS</h1>
        <h2>Laporan Kehadiran Kegiatan Santri</h2>
        <p>Periode: {{ $periodeLabel }} | Dicetak: {{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</p>
    </div>

    {{-- KPI SUMMARY --}}
    <div class="section">
        <div class="section-title">Ringkasan</div>
        <div class="kpi-row">
            <div class="kpi-box">
                <div class="inner">
                    <div class="label">Total Kegiatan</div>
                    <div class="value">{{ $kpi['total_kegiatan'] }}</div>
                </div>
            </div>
            <div class="kpi-box">
                <div class="inner">
                    <div class="label">Rata-rata Kehadiran</div>
                    <div class="value">{{ $kpi['avg_kehadiran'] }}%</div>
                </div>
            </div>
            <div class="kpi-box">
                <div class="inner">
                    <div class="label">Kegiatan Terbaik</div>
                    <div class="value" style="font-size:12px;">{{ $kpi['kegiatan_terbaik']['nama'] }}</div>
                    <div class="sub">{{ $kpi['kegiatan_terbaik']['persen'] }}%</div>
                </div>
            </div>
            <div class="kpi-box">
                <div class="inner">
                    <div class="label">Perlu Perhatian</div>
                    <div class="value" style="color:#EF4444;">{{ $kpi['santri_perlu_perhatian'] }}</div>
                    <div class="sub">santri &lt;70%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOP & BOTTOM KEGIATAN --}}
    <div class="section">
        <div class="section-title">Top 5 Kegiatan Terbaik</div>
        <table>
            <thead><tr><th>No</th><th>Kegiatan</th><th>Kategori</th><th class="text-center">Total</th><th class="text-center">Hadir</th><th class="text-center">% Kehadiran</th></tr></thead>
            <tbody>
                @foreach($topKegiatan as $i => $kg)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td><strong>{{ $kg['nama_kegiatan'] }}</strong></td>
                        <td>{{ $kg['nama_kategori'] ?? '-' }}</td>
                        <td class="text-center">{{ $kg['total'] }}</td>
                        <td class="text-center">{{ $kg['hadir'] }}</td>
                        <td class="text-center"><span class="badge badge-success">{{ $kg['persen'] }}%</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">5 Kegiatan Perlu Evaluasi</div>
        <table>
            <thead><tr><th>No</th><th>Kegiatan</th><th>Kategori</th><th class="text-center">Total</th><th class="text-center">Hadir</th><th class="text-center">% Kehadiran</th></tr></thead>
            <tbody>
                @foreach($bottomKegiatan as $i => $kg)
                    <tr class="{{ $kg['persen'] < 70 ? 'highlight-row' : '' }}">
                        <td>{{ $i+1 }}</td>
                        <td><strong>{{ $kg['nama_kegiatan'] }}</strong></td>
                        <td>{{ $kg['nama_kategori'] ?? '-' }}</td>
                        <td class="text-center">{{ $kg['total'] }}</td>
                        <td class="text-center">{{ $kg['hadir'] }}</td>
                        <td class="text-center"><span class="badge badge-danger">{{ $kg['persen'] }}%</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- KEHADIRAN PER KELAS --}}
    <div class="section">
        <div class="section-title">Kehadiran Per Kelas</div>
        @foreach($kehadiranPerKelas as $kelompok)
            <p style="font-weight:bold; margin: 8px 0 4px; color:#10B981;">{{ $kelompok['nama_kelompok'] }}</p>
            <table>
                <thead><tr><th>Kelas</th><th class="text-center">Santri</th><th class="text-center">Hadir</th><th class="text-center">Izin</th><th class="text-center">Sakit</th><th class="text-center">Alpa</th><th class="text-center">%</th></tr></thead>
                <tbody>
                    @foreach($kelompok['kelas'] as $k)
                        <tr class="{{ $k['persen'] < 70 ? 'highlight-row' : '' }}">
                            <td><strong>{{ $k['nama_kelas'] }}</strong></td>
                            <td class="text-center">{{ $k['jumlah_santri'] }}</td>
                            <td class="text-center">{{ $k['hadir'] }}</td>
                            <td class="text-center">{{ $k['izin'] }}</td>
                            <td class="text-center">{{ $k['sakit'] }}</td>
                            <td class="text-center">{{ $k['alpa'] }}</td>
                            <td class="text-center">
                                <span class="badge {{ $k['persen'] >= 85 ? 'badge-success' : ($k['persen'] >= 70 ? 'badge-warning' : 'badge-danger') }}">{{ $k['persen'] }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>

    {{-- DISTRIBUSI SANTRI --}}
    <div class="section">
        <div class="section-title">Distribusi Kehadiran Santri</div>
        <table>
            <thead><tr><th>Kategori</th><th class="text-center">Jumlah Santri</th><th class="text-center">Persentase</th></tr></thead>
            <tbody>
                @foreach($distribusiSantri as $d)
                    <tr>
                        <td><strong>{{ $d['label'] }}</strong></td>
                        <td class="text-center">{{ $d['count'] }}</td>
                        <td class="text-center">{{ $d['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- SANTRI PERLU PERHATIAN --}}
    @if($santriPerluPerhatianList && $santriPerluPerhatianList->count() > 0)
    <div class="section">
        <div class="section-title">Santri Perlu Perhatian (&lt;70% kehadiran)</div>
        <table>
            <thead><tr><th>No</th><th>ID</th><th>Nama</th><th class="text-center">Alpa</th><th class="text-center">% Kehadiran</th></tr></thead>
            <tbody>
                @foreach($santriPerluPerhatianList as $i => $s)
                    <tr class="highlight-row">
                        <td>{{ $i+1 }}</td>
                        <td>{{ $s->id_santri }}</td>
                        <td><strong>{{ $s->nama_lengkap }}</strong></td>
                        <td class="text-center"><span class="badge badge-danger">{{ $s->alpa }}x</span></td>
                        <td class="text-center"><span class="badge badge-danger">{{ $s->persen }}%</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <span>SIM-PKPPS - Laporan Otomatis</span>
        <span>Dicetak pada {{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
    </div>
</body>
</html>
