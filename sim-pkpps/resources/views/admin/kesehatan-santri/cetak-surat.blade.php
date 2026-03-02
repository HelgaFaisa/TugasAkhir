<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.4;
            padding: 12px 25px 5px;
            color: #000;
            font-size: 11pt;
        }

        /* ── KOP SURAT ── */
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
        .kop-text .nama-lembaga { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .kop-text .unit { font-size: 11pt; margin: 1px 0; color: #222; }
        .kop-text .alamat { font-size: 9pt; color: #333; line-height: 15px; margin-top: 3px; }

        .reference-number { margin: 5px 0 0; font-weight: bold; font-size: 10.5pt; }

        .title { text-align: center; margin: 7px 0 6px; }
        .title h3 { font-size: 13pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; letter-spacing: 1px; }

        .content { margin: 5px 0; text-align: justify; font-size: 11pt; }
        .content p { margin: 5px 0; text-indent: 25px; }

        .data-table { width: 100%; margin: 6px 0; border-collapse: collapse; font-size: 11pt; }
        .data-table td { padding: 2px 8px; vertical-align: top; }
        .data-table .label { width: 170px; font-weight: bold; }
        .data-table .colon { width: 15px; text-align: center; }

        .recommendation-box {
            margin: 4px 0 4px 25px;
            padding: 8px 12px;
            border-left: 3px solid #6FBA9D;
            background-color: #F8FBF9;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        .date { text-align: right; margin: 6px 0 2px; font-size: 11pt; }

        .ttd { margin-top: 5px; display: flex; justify-content: flex-end; }
        .ttd-block { text-align: center; width: 42%; font-size: 11pt; }
        .ttd-space { height: 45px; }

        .footer-note {
            margin-top: 5px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }

        @media print {
            body { padding: 8px 18px; }
            .no-print { display: none; }
            @page { margin: 0.8cm; size: A4; }
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop">
        <img src="{{ asset('images/logo.png') }}" alt="Logo PKPPS Riyadlul Jannah">
        <div class="kop-text">
            <div class="nama-lembaga">PKPPS Riyadlul Jannah</div>
            <div class="unit">Unit Kesehatan Pesantren (UKP)</div>
            <div class="alamat">
                Jl. Raya Brangkal No. 42 RT. 02 RW. 01, Desa Brangkal, Kec. Sooko, Kab. Mojokerto, Prov. Jawa Timur
            </div>
        </div>
    </div>

    <!-- Nomor Surat -->
    <div class="reference-number">
        Nomor: UKP/{{ str_pad($kesehatanSantri->id, 3, '0', STR_PAD_LEFT) }}/{{ $kesehatanSantri->tanggal_masuk->format('m/Y') }}
    </div>

    <!-- Judul -->
    <div class="title">
        <h3>Surat Keterangan Sakit</h3>
    </div>

    <!-- Isi -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Petugas Unit Kesehatan Pesantren (UKP) PKPPS Riyadlul Jannah, dengan ini menerangkan bahwa:</p>

        <table class="data-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td><strong>{{ $kesehatanSantri->santri->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td class="label">NIS</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->santri->nis ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->santri->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Masuk UKP</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->tanggal_masuk->locale('id')->isoFormat('D MMMM Y') }}</td>
            </tr>
            @if($kesehatanSantri->tanggal_keluar)
            <tr>
                <td class="label">Tanggal Keluar UKP</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->tanggal_keluar->locale('id')->isoFormat('D MMMM Y') }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Lama Dirawat</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->lama_dirawat }} hari</td>
            </tr>
        </table>

        <p><strong>Keluhan:</strong> <em>{{ $kesehatanSantri->keluhan }}</em></p>

        @if($kesehatanSantri->catatan)
        <p><strong>Catatan Petugas:</strong> <em>{{ $kesehatanSantri->catatan }}</em></p>
        @endif

        <p>Berdasarkan kondisi kesehatan santri tersebut, kami merekomendasikan:</p>

        <div class="recommendation-box">
            @if($kesehatanSantri->status == 'izin')
                &#10003; <strong>Diberikan izin istirahat/pulang</strong> selama masa pemulihan<br>
                &#10003; Tidak mengikuti kegiatan fisik berat<br>
                &#10003; Kontrol kembali jika gejala memburuk
            @elseif($kesehatanSantri->status == 'sembuh')
                &#10003; <strong>Sudah sembuh dan dapat mengikuti kegiatan normal</strong><br>
                &#10003; Tetap jaga kesehatan dan istirahat cukup<br>
                &#10003; Konsultasi jika ada keluhan lanjutan
            @else
                &#10003; <strong>Masih dalam perawatan UKP</strong><br>
                &#10003; Memerlukan istirahat dan pengobatan lanjutan<br>
                &#10003; Izin tidak mengikuti kegiatan normal
            @endif
        </div>

        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk digunakan sebagaimana mestinya.</p>
    </div>

    <!-- Tanggal -->
    <div class="date">
        Mojokerto, {{ $kesehatanSantri->tanggal_masuk->locale('id')->isoFormat('D MMMM Y') }}
    </div>

    <!-- Tanda Tangan -->
    <div class="ttd">
        <div class="ttd-block">
            <p><strong>Petugas UKP</strong></p>
            <div class="ttd-space"></div>
            <p><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong></p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        &nbsp; Dicetak: {{ now()->locale('id')->isoFormat('D MMMM Y') }} WIB
    </div>


    <!-- Tombol Print -->
    <div class="no-print" style="position:fixed; top:10px; right:10px; z-index:1000;">
        <button onclick="window.print()" style="background:#6FBA9D; color:white; border:none; padding:10px 15px; border-radius:8px; cursor:pointer; font-size:14px; box-shadow:0 2px 4px rgba(0,0,0,.2);">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" style="background:#E74C3C; color:white; border:none; padding:10px 15px; border-radius:8px; cursor:pointer; font-size:14px; margin-left:5px; box-shadow:0 2px 4px rgba(0,0,0,.2);">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <script>
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') { e.preventDefault(); window.print(); }
        });
    </script>
</body>
</html>