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

        /* ── KOP SURAT ─────────────────────────────────── */
        .kop { display: flex; align-items: center; gap: 16px; padding-bottom: 12px; border-bottom: 3px double #6FBA9D; margin-bottom: 8px; }
        .kop img { width: 72px; height: 72px; object-fit: contain; flex-shrink: 0; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .nama-lembaga { font-size: 17px; font-weight: bold; color: #1a1a1a; letter-spacing: .4px; text-transform: uppercase; }
        .kop-text .nama-singkat { font-size: 13px; color: #555; margin: 2px 0; }
        .kop-text .alamat { font-size: 10.5px; color: #555; line-height: 16px; margin-top: 4px; }

        /* ── JUDUL DOKUMEN ─────────────────────────────── */
        .header { text-align: center; margin: 14px 0 20px; }
        .header h2 { font-size: 15px; color: #6FBA9D; letter-spacing: 2px; text-transform: uppercase; }
        .header p { font-size: 12px; color: #666; margin-top: 4px; }

        .bukti-info { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .bukti-info table { width: 100%; }
        .bukti-info tr { line-height: 28px; }
        .bukti-info td:first-child { font-weight: bold; width: 160px; color: #555; }
        .bukti-info td:nth-child(2) { width: 20px; }
        .bukti-info td:last-child { color: #333; }

        .nominal-box { background: linear-gradient(135deg, #6FBA9D 0%, #8FCAAE 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; margin: 25px 0; }
        .nominal-box h3 { font-size: 14px; margin-bottom: 10px; opacity: .9; }
        .nominal-box .amount { font-size: 32px; font-weight: bold; letter-spacing: 1px; }

        .status-box { text-align: center; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .status-lunas { background: #d4edda; color: #155724; border: 2px solid #c3e6cb; }
        .status-belum { background: #fff3cd; color: #856404; border: 2px solid #ffeaa7; }

        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px dashed #ddd; }
        .footer p { font-size: 11px; color: #888; line-height: 18px; }

        .ttd-section { margin-top: 40px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; width: 45%; }
        .ttd-box p { margin-bottom: 60px; font-size: 12px; }
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
    <div class="no-print" style="text-align:center; margin-bottom:20px;">
        <button onclick="window.print()" style="padding:12px 30px; background:#6FBA9D; color:white; border:none; border-radius:5px; cursor:pointer; font-size:14px;">
            Cetak Bukti
        </button>
    </div>

    <div class="bukti-container">

        <!-- ══ KOP SURAT ══ -->
        <div class="kop">
            <img src="{{ asset('images/logo.png') }}" alt="Logo PKPPS Riyadlul Jannah">
            <div class="kop-text">
                <div class="nama-lembaga">PKPPS Riyadlul Jannah Mojokerto</div>
                <div class="alamat">
                    Jl. Raya Brangkal No. 42 RT. 02 RW. 01, Desa Brangkal<br>
                    Kec. Sooko, Kab. Mojokerto, Prov. Jawa Timur
                </div>
            </div>
        </div>

        <!-- ══ JUDUL DOKUMEN ══ -->
        <div class="header">
            <h2>Bukti Pembayaran SPP</h2>
            <p>No. Bukti: <strong>{{ $pembayaranSpp->id_pembayaran }}</strong></p>
        </div>

        <!-- Status -->
        @if($pembayaranSpp->status === 'Lunas')
            <div class="status-box status-lunas">
                <strong style="font-size:16px;">&#10003; LUNAS</strong>
            </div>
        @else
            <div class="status-box status-belum">
                <strong style="font-size:16px;">&#9888; BELUM LUNAS</strong>
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
                            <span style="color:#999;">Belum dibayar</span>
                        @endif
                    </td>
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

</body>
</html>