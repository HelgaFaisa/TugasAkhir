{{-- resources/views/admin/pembayaran-spp/cetak-laporan-santri.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran SPP - {{ $santri->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 12px; }
        
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 20px; margin-bottom: 5px; }
        .header h2 { font-size: 16px; color: #666; margin-bottom: 10px; }
        .header p { font-size: 11px; color: #888; }
        
        .santri-info { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .santri-info table { width: 100%; }
        .santri-info td { padding: 5px; }
        .santri-info td:first-child { font-weight: bold; width: 150px; }
        
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .stat-card { text-align: center; flex: 1; padding: 15px; background: #f9f9f9; border-radius: 5px; margin: 0 5px; }
        .stat-card h3 { font-size: 11px; color: #666; margin-bottom: 8px; }
        .stat-card .value { font-size: 18px; font-weight: bold; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #6FBA9D; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #888; border-top: 1px solid #ddd; padding-top: 10px; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>
    <!-- Tombol Print -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #6FBA9D; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Laporan
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>PONDOK PESANTREN PKPPS</h1>
        <h2>LAPORAN PEMBAYARAN SPP SANTRI</h2>
        <p>Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Info Santri -->
    <div class="santri-info">
        <table>
            <tr>
                <td>ID Santri</td>
                <td>: {{ $santri->id_santri }}</td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td>: <strong>{{ $santri->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: {{ $santri->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $santri->kelas_lengkap }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ $santri->status }}</td>
            </tr>
        </table>
    </div>

    <!-- Statistik -->
    <div class="stats">
        <div class="stat-card">
            <h3>Total Terbayar</h3>
            <div class="value" style="color: #28a745;">Rp {{ number_format($totalLunas, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <h3>Total Tunggakan</h3>
            <div class="value" style="color: #dc3545;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <h3>Pembayaran Telat</h3>
            <div class="value" style="color: #ffc107;">{{ $jumlahTelat }}</div>
        </div>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">ID Pembayaran</th>
                <th width="20%">Periode</th>
                <th width="18%">Nominal</th>
                <th width="14%">Batas Bayar</th>
                <th width="14%">Tanggal Bayar</th>
                <th width="14%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayaranSpp as $index => $spp)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $spp->id_pembayaran }}</td>
                <td>{{ $spp->periode_lengkap }}</td>
                <td class="text-right"><strong>{{ $spp->nominal_format }}</strong></td>
                <td class="text-center">{{ $spp->batas_bayar->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($spp->tanggal_bayar)
                        {{ $spp->tanggal_bayar->format('d/m/Y') }}
                    @else
                        <span style="color: #999;">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($spp->status === 'Lunas')
                        <span class="badge badge-success">Lunas</span>
                    @elseif($spp->isTelat())
                        <span class="badge badge-danger">Telat</span>
                    @else
                        <span class="badge badge-warning">Belum Lunas</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 30px; color: #999;">
                    Belum ada riwayat pembayaran untuk santri ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} Pondok Pesantren PKPPS. Laporan dicetak otomatis oleh sistem.</p>
    </div>
</body>
</html>