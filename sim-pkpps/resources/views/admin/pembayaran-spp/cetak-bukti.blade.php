{{-- resources/views/admin/pembayaran-spp/cetak-bukti.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran SPP - {{ $pembayaranSpp->id_pembayaran }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 30px; font-size: 13px; }
        
        .bukti-container { max-width: 600px; margin: 0 auto; border: 3px solid #6FBA9D; border-radius: 10px; padding: 25px; }
        
        .header { text-align: center; border-bottom: 2px solid #6FBA9D; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { font-size: 22px; color: #333; margin-bottom: 5px; }
        .header h2 { font-size: 18px; color: #6FBA9D; margin-bottom: 10px; }
        .header p { font-size: 12px; color: #666; }
        
        .bukti-info { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .bukti-info table { width: 100%; }
        .bukti-info tr { line-height: 28px; }
        .bukti-info td:first-child { font-weight: bold; width: 160px; color: #555; }
        .bukti-info td:nth-child(2) { width: 20px; }
        .bukti-info td:last-child { color: #333; }
        
        .nominal-box { background: linear-gradient(135deg, #6FBA9D 0%, #8FCAAE 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; margin: 25px 0; }
        .nominal-box h3 { font-size: 14px; margin-bottom: 10px; opacity: 0.9; }
        .nominal-box .amount { font-size: 32px; font-weight: bold; letter-spacing: 1px; }
        
        .status-box { text-align: center; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .status-lunas { background: #d4edda; color: #155724; border: 2px solid #c3e6cb; }
        .status-belum { background: #fff3cd; color: #856404; border: 2px solid #ffeaa7; }
        
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px dashed #ddd; }
        .footer p { font-size: 11px; color: #888; line-height: 18px; }
        
        .ttd-section { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd-box { text-align: center; width: 45%; }
        .ttd-box p { margin-bottom: 80px; font-size: 12px; }
        .ttd-box strong { font-size: 13px; border-top: 1px solid #333; padding-top: 5px; display: inline-block; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>
    <!-- Tombol Print -->
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 12px 30px; background: #6FBA9D; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            <i class="fas fa-print"></i> Cetak Bukti
        </button>
    </div>

    <div class="bukti-container">
        <!-- Header -->
        <div class="header">
            <h1>PONDOK PESANTREN PKPPS</h1>
            <h2>BUKTI PEMBAYARAN SPP</h2>
            <p>No. Bukti: <strong>{{ $pembayaranSpp->id_pembayaran }}</strong></p>
        </div>

        <!-- Status -->
        @if($pembayaranSpp->status === 'Lunas')
        <div class="status-box status-lunas">
            <strong style="font-size: 16px;">✓ LUNAS</strong>
        </div>
        @else
        <div class="status-box status-belum">
            <strong style="font-size: 16px;">⚠ BELUM LUNAS</strong>
        </div>
        @endif

        <!-- Info Santri & Pembayaran -->
        <div class="bukti-info">
            <table>
                <tr>
                    <td>ID Santri</td>
                    <td>:</td>
                    <td>{{ $pembayaranSpp->santri->id_santri }}</td>
                </tr>
                <tr>
                    <td>Nama Santri</td>
                    <td>:</td>
                    <td><strong>{{ $pembayaranSpp->santri->nama_lengkap }}</strong></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td>{{ $pembayaranSpp->santri->kelas_lengkap }}</td>
                </tr>
                <tr>
                    <td>Periode Pembayaran</td>
                    <td>:</td>
                    <td><strong>{{ $pembayaranSpp->periode_lengkap }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Bayar</td>
                    <td>:</td>
                    <td>
                        @if($pembayaranSpp->tanggal_bayar)
                            {{ $pembayaranSpp->tanggal_bayar->format('d F Y') }}
                        @else
                            <span style="color: #999;">Belum dibayar</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Batas Pembayaran</td>
                    <td>:</td>
                    <td>{{ $pembayaranSpp->batas_bayar->format('d F Y') }}</td>
                </tr>
                @if($pembayaranSpp->keterangan)
                <tr>
                    <td>Keterangan</td>
                    <td>:</td>
                    <td>{{ $pembayaranSpp->keterangan }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Nominal -->
        <div class="nominal-box">
            <h3>JUMLAH PEMBAYARAN</h3>
            <div class="amount">{{ $pembayaranSpp->nominal_format }}</div>
        </div>

        <!-- Tanda Tangan -->
        <div class="ttd-section">
            <div class="ttd-box">
                <p>Santri / Wali Santri</p>
                <strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </div>
            <div class="ttd-box">
                <p>Petugas</p>
                <strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>PERHATIAN:</strong><br>
                Bukti pembayaran ini sah dan merupakan tanda terima yang diakui secara resmi.<br>
                Simpan bukti ini sebagai arsip pembayaran SPP.<br>
                Dicetak pada: {{ date('d F Y, H:i') }} WIB
            </p>
        </div>
    </div>

    <script>
        // Auto print (opsional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>