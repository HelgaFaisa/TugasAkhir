{{-- resources/views/admin/kepulangan/surat-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Kepulangan - {{ $kepulangan->id_kepulangan }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 20mm;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 16pt;
            margin: 5px 0;
        }
        .header p {
            font-size: 10pt;
            margin: 2px 0;
        }
        .title {
            text-align: center;
            margin: 30px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .nomor {
            text-align: center;
            margin-bottom: 30px;
            font-size: 12pt;
        }
        .content {
            margin-bottom: 30px;
            text-align: justify;
        }
        .content p {
            margin-bottom: 15px;
        }
        .data-santri {
            margin: 20px 0;
            padding-left: 50px;
        }
        .data-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .data-label {
            display: table-cell;
            width: 150px;
            vertical-align: top;
        }
        .data-separator {
            display: table-cell;
            width: 20px;
            vertical-align: top;
        }
        .data-value {
            display: table-cell;
            vertical-align: top;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-row {
            display: table;
            width: 100%;
        }
        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 80px;
            margin: 20px 0 10px;
        }
        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 150px;
            padding-bottom: 2px;
        }
        .footer {
            position: fixed;
            bottom: 10mm;
            left: 20mm;
            right: 20mm;
            font-size: 8pt;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            color: #666;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72pt;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
            .footer { position: fixed; }
        }
    </style>
</head>
<body>
    {{-- Watermark --}}
    <div class="watermark">{{ strtoupper($kepulangan->status) }}</div>
    
    {{-- Header --}}
    <div class="header">
        <h1>Pondok Pesantren Al-Hikmah</h1>
        <h2>Yayasan Pendidikan Islam</h2>
        <p>Jl. Raya Pesantren No. 123, Jakarta Selatan</p>
        <p>Telp: (021) 123456 | Email: info@alhikmah.ac.id</p>
    </div>
    
    {{-- Title --}}
    <div class="title">
        Surat Izin Kepulangan Santri
    </div>
    
    {{-- Nomor Surat --}}
    <div class="nomor">
        Nomor: {{ $kepulangan->id_kepulangan }}/IZIN/{{ $kepulangan->tanggal_pulang->format('m') }}/{{ $kepulangan->tanggal_pulang->year }}
    </div>
    
    {{-- Content --}}
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Pengurus Pondok Pesantren Al-Hikmah, dengan ini menerangkan bahwa:</p>
        
        <div class="data-santri">
            <div class="data-row">
                <div class="data-label">Nama</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $santri->nama_lengkap }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">NIS</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $santri->nis ?? '-' }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">ID Santri</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $santri->id_santri }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Kelas</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $santri->kelas }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Jenis Kelamin</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $santri->jenis_kelamin }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Alamat</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $santri->alamat_santri ?? $santri->daerah_asal ?? '-' }}</div>
            </div>
        </div>
        
        <p>Adalah benar-benar santri aktif di Pondok Pesantren Al-Hikmah dan telah mendapat izin untuk pulang ke rumah dengan keterangan sebagai berikut:</p>
        
        <div class="data-santri">
            <div class="data-row">
                <div class="data-label">Tanggal Pulang</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->tanggal_pulang->format('d F Y') }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Tanggal Kembali</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->tanggal_kembali->format('d F Y') }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Durasi Izin</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->durasi_izin_calculated }} hari</div>
            </div>
            <div class="data-row">
                <div class="data-label">Alasan Kepulangan</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->alasan }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Status Izin</div>
                <div class="data-separator">:</div>
                <div class="data-value status-approved">{{ $kepulangan->status }}</div>
            </div>
            @if($kepulangan->catatan)
            <div class="data-row">
                <div class="data-label">Catatan</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->catatan }}</div>
            </div>
            @endif
        </div>
        
        <p>Demikian surat izin ini dibuat untuk dapat digunakan sebagaimana mestinya. Santri yang bersangkutan <strong>WAJIB</strong> kembali pada tanggal yang telah ditentukan.</p>
        
        <p><strong>Catatan Penting:</strong></p>
        <ul style="margin-left: 30px;">
            <li>Santri wajib kembali sesuai tanggal yang tertera dalam surat ini</li>
            <li>Apabila terlambat kembali tanpa pemberitahuan, akan dikenakan sanksi sesuai peraturan pesantren</li>
            <li>Surat ini harus dibawa dan ditunjukkan kepada petugas jaga saat keluar dan masuk pesantren</li>
            <li>Orang tua/wali santri bertanggung jawab penuh selama santri berada di rumah</li>
        </ul>
    </div>
    
    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-left">
                <p>Mengetahui,<br>Wali Santri</p>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $santri->nama_orang_tua ?? '........................' }}</div>
            </div>
            <div class="signature-right">
                <p>Jakarta, {{ $tanggalCetak }}</p>
                <p>Pengurus Pondok Pesantren</p>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $kepulangan->approved_by ?? 'Admin' }}</div>
                <p style="margin-top: 5px; font-size: 10pt;">Bidang Kesiswaan</p>
            </div>
        </div>
    </div>
    
    {{-- Footer --}}
    <div class="footer">
        <p>
            Dicetak pada: {{ $tanggalCetak }} | 
            ID: {{ $kepulangan->id_kepulangan }} | 
            Status: {{ $kepulangan->status }} | 
            Surat ini sah tanpa tanda tangan basah
        </p>
    </div>
</body>
</html>