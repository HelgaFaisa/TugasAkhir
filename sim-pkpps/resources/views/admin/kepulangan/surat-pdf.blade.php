<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Kepulangan - {{ $kepulangan->id_kepulangan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11.5pt;
            line-height: 1.5;
            padding: 12px 28px 8px;
            color: #000;
        }

        /* KOP */
        .kop {
            position: relative;
            padding-bottom: 10px;
            border-bottom: 4px double #000;
            margin-bottom: 6px;
            min-height: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .kop img {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .kop-text { text-align: center; }
        .kop-text .nama   { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .kop-text .sub    { font-size: 11pt; margin: 1px 0; color: #222; }
        .kop-text .alamat { font-size: 9pt; color: #333; line-height: 15px; margin-top: 3px; }

        .nomor { font-size: 10.5pt; margin: 7px 0 0; }
        .judul {
            text-align: center;
            font-size: 13.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px 0 8px;
        }

        .pembuka { text-indent: 30px; margin-bottom: 6px; }

        .data-table { width: 100%; border-collapse: collapse; margin: 4px 0 4px 20px; }
        .data-table td { padding: 2px 6px; vertical-align: top; font-size: 11.5pt; }
        .data-table .lbl { width: 185px; }
        .data-table .sep { width: 14px; }

        .info-box {
            margin: 6px 0;
            padding: 7px 12px;
            border-left: 3px solid #f0a500;
            background: #fffdf0;
            font-size: 10.5pt;
            line-height: 1.5;
        }
        .info-box ul { margin: 3px 0 0 18px; }
        .info-box li  { margin-bottom: 2px; }

        .penutup { text-indent: 30px; margin: 6px 0 4px; }
        .tanggal { text-align: right; margin: 6px 0 2px; }

        .footer-note {
            margin-top: 8px;
            text-align: center;
            font-size: 8.5pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        @media print {
            body { padding: 8px 22px 5px; }
            @page { margin: 0.8cm; size: A4; }
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-text">
            <div class="nama">PKPPS Riyadlul Jannah</div>
            <div class="sub">Islamic Boarding School</div>
            <div class="alamat">
                Jl. Raya Brangkal No. 42 RT. 02 RW. 01, Desa Brangkal, Kec. Sooko, Kab. Mojokerto, Prov. Jawa Timur
            </div>
        </div>
    </div>

    <!-- Nomor -->
    <div class="nomor">
        Nomor: {{ $kepulangan->id_kepulangan }}/IZIN-PULANG/PKPPS-RJ/{{ $kepulangan->tanggal_pulang->format('m/Y') }}
    </div>

    <!-- Judul -->
    <div class="judul">Surat Izin Kepulangan Santri</div>

    <!-- Pembuka -->
    <p class="pembuka">
        Yang bertanda tangan di bawah ini, Pengurus PKPPS Riyadlul Jannah, dengan ini memberikan izin kepulangan kepada santri berikut:
    </p>

    <!-- Data -->
    <table class="data-table">
        <tr>
            <td class="lbl">Nama Lengkap</td>
            <td class="sep">:</td>
            <td><strong>{{ $santri->nama_lengkap }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">NIS</td>
            <td class="sep">:</td>
            <td>{{ $santri->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td>{{ $santri->alamat_santri ?? $santri->daerah_asal ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal Pulang</td>
            <td class="sep">:</td>
            <td>{{ $kepulangan->tanggal_pulang->locale('id')->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal Wajib Kembali</td>
            <td class="sep">:</td>
            <td>{{ $kepulangan->tanggal_kembali->locale('id')->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Lama Izin</td>
            <td class="sep">:</td>
            <td>{{ $kepulangan->durasi_izin }} hari</td>
        </tr>
        <tr>
            <td class="lbl">Alasan Kepulangan</td>
            <td class="sep">:</td>
            <td>{{ $kepulangan->alasan }}</td>
        </tr>
        @if($kepulangan->catatan)
        <tr>
            <td class="lbl">Catatan</td>
            <td class="sep">:</td>
            <td>{{ $kepulangan->catatan }}</td>
        </tr>
        @endif
    </table>

    <!-- Ketentuan -->
    <div class="info-box">
        <strong>Ketentuan:</strong>
        <ul>
            <li>Santri <strong>WAJIB</strong> kembali sesuai tanggal yang tertera di atas</li>
            <li>Keterlambatan tanpa pemberitahuan akan dikenakan sanksi sesuai peraturan pesantren</li>
            <li>Tunjukkan surat ini kepada petugas jaga saat keluar dan masuk pesantren</li>
        </ul>
    </div>

    <!-- Penutup -->
    <p class="penutup">Demikian surat izin ini dibuat untuk digunakan sebagaimana mestinya.</p>

    <!-- Tanggal -->
    <div class="tanggal">
        Mojokerto, {{ now()->locale('id')->isoFormat('D MMMM Y') }}
    </div>

    <!-- TTD jejer pakai table -->
    <table style="width:100%; margin-top:8px; border-collapse:collapse;">
        <tr>
            <td style="width:50%; text-align:center; padding:0 20px;">
                <p><strong>Saksi</strong></p>
                <div style="height:55px;"></div>
                <p><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong></p>
            </td>
            <td style="width:50%; text-align:center; padding:0 20px;">
                <p><strong>Petugas Pesantren</strong></p>
                <div style="height:55px;"></div>
                <p><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong></p>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer-note">
        Dicetak: {{ now()->locale('id')->isoFormat('D MMMM Y') }} WIB &nbsp;|&nbsp; ID: {{ $kepulangan->id_kepulangan }} &nbsp;|&nbsp; Surat ini sah tanpa tanda tangan basah
    </div>



    <script>
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') { e.preventDefault(); window.print(); }
        });
    </script>
</body>
</html>