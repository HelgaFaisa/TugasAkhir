<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Sakit - {{ $kesehatanSantri->santri->nama_lengkap }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #000;
            font-size: 12pt;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: normal;
            margin: 5px 0;
            color: #333;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 11pt;
        }
        
        .clear {
            clear: both;
        }
        
        .title {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        
        .title h3 {
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        
        .content p {
            margin: 10px 0;
            text-indent: 30px;
        }
        
        .data-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 5px 10px;
            vertical-align: top;
        }
        
        .data-table .label {
            width: 180px;
            font-weight: bold;
        }
        
        .data-table .colon {
            width: 20px;
            text-align: center;
        }
        
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-block {
            width: 45%;
            text-align: center;
        }
        
        .signature-space {
            height: 60px;
            margin: 15px 0;
        }
        
        .date {
            text-align: right;
            margin: 20px 0;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .stamp-area {
            border: 2px solid #000;
            width: 150px;
            height: 100px;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10pt;
            color: #666;
        }
        
        .reference-number {
            text-align: left;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .recommendation-box {
            margin-left: 30px;
            padding: 15px;
            border-left: 4px solid #6FBA9D;
            background-color: #F8FBF9;
        }
    </style>
</head>
<body>
    <!-- Header Surat -->
    <div class="header">
        <h1>Pondok Pesantren [Nama Pesantren]</h1>
        <h2>Unit Kesehatan Pesantren (UKP)</h2>
        <p>Alamat: [Alamat Lengkap Pesantren]</p>
        <p>Telepon: [Nomor Telepon] | Email: [Email Pesantren]</p>
    </div>

    <!-- Nomor Surat -->
    <div class="reference-number">
        Nomor: UKP/{{ str_pad($kesehatanSantri->id, 3, '0', STR_PAD_LEFT) }}/{{ $kesehatanSantri->tanggal_masuk->format('m/Y') }}
    </div>

    <!-- Judul Surat -->
    <div class="title">
        <h3>Surat Keterangan Sakit</h3>
    </div>

    <!-- Isi Surat -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Petugas Unit Kesehatan Pesantren (UKP) 
           Pondok Pesantren [Nama Pesantren], dengan ini menerangkan bahwa:</p>

        <table class="data-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td><strong>{{ $kesehatanSantri->santri->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td class="label">ID Santri</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->santri->id_santri }}</td>
            </tr>
            <tr>
                <td class="label">NIS</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->santri->nis ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="colon">:</td>
                <td>{{ $kesehatanSantri->santri->kelas }}</td>
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

        <p><strong>Keluhan/Diagnosa:</strong></p>
        <p style="margin-left: 30px; border-left: 3px solid #ccc; padding-left: 15px; font-style: italic;">
            {{ $kesehatanSantri->keluhan }}
        </p>

        @if($kesehatanSantri->catatan)
        <p><strong>Catatan Petugas:</strong></p>
        <p style="margin-left: 30px; border-left: 3px solid #ccc; padding-left: 15px; font-style: italic;">
            {{ $kesehatanSantri->catatan }}
        </p>
        @endif

        <p>Berdasarkan kondisi kesehatan santri tersebut, kami merekomendasikan untuk:</p>
        
        <div class="recommendation-box">
            @if($kesehatanSantri->status == 'izin')
                ✓ <strong>Diberikan izin untuk istirahat/pulang</strong> selama masa pemulihan<br>
                ✓ Tidak mengikuti kegiatan fisik yang berat<br>
                ✓ Kontrol kembali jika gejala memburuk
            @elseif($kesehatanSantri->status == 'sembuh')
                ✓ <strong>Sudah sembuh dan dapat mengikuti kegiatan normal</strong><br>
                ✓ Tetap menjaga kesehatan dan istirahat yang cukup<br>
                ✓ Konsultasi jika ada keluhan lanjutan
            @else
                ✓ <strong>Masih dalam perawatan di UKP</strong><br>
                ✓ Memerlukan istirahat dan pengobatan lanjutan<br>
                ✓ Belum diperkenankan mengikuti kegiatan normal
            @endif
        </div>

        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat digunakan sebagaimana mestinya.</p>
    </div>

    <!-- Tanggal dan Tanda Tangan -->
    <div class="date">
        {{ $kesehatanSantri->tanggal_masuk->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </div>

    <div class="signature">
        <div class="signature-block">
            <p><strong>Wali Santri/Keluarga</strong></p>
            <div class="signature-space"></div>
            <p><strong>{{ $kesehatanSantri->santri->nama_orang_tua ?: '(...............................)' }}</strong></p>
            <hr style="width: 150px; margin: 0 auto;">
        </div>
        
        <div class="signature-block">
            <p><strong>Petugas UKP</strong></p>
            <div class="signature-space"></div>
            <div class="stamp-area">
                <span>Cap & Tanda Tangan</span>
            </div>
            <p><strong>[Nama Petugas UKP]</strong></p>
            <hr style="width: 150px; margin: 0 auto;">
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 10pt; color: #666; border-top: 1px solid #ccc; padding-top: 10px;">
        <p>Surat ini dibuat secara elektronis dan sah tanpa tanda tangan basah</p>
        <p>Dicetak pada: {{ now()->locale('id')->isoFormat('D MMMM Y HH:mm:ss') }} WIB</p>
    </div>

    <!-- Print Button (tidak akan tercetak) -->
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
        <button onclick="window.print()" style="background: #6FBA9D; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" style="background: #E74C3C; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-size: 14px; margin-left: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <script>
        // Print function
        function printSurat() {
            window.print();
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P or Cmd+P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>