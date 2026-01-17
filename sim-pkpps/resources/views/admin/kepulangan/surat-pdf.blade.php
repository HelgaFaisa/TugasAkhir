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
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            display: block;
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
            font-weight: normal;
        }
        
        .header p {
            font-size: 10pt;
            margin: 2px 0;
        }
        
        .title {
            text-align: center;
            margin: 30px 0 20px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .nomor {
            text-align: center;
            margin-bottom: 30px;
            font-size: 11pt;
        }
        
        .content {
            margin-bottom: 30px;
            text-align: justify;
        }
        
        .content p {
            margin-bottom: 15px;
        }
        
        .data-santri {
            margin: 20px 0 20px 50px;
        }
        
        .data-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .data-label {
            display: table-cell;
            width: 180px;
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
            margin-top: 60px;
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
            padding: 0 20px;
        }
        
        .signature-space {
            height: 80px;
            margin: 20px 0 10px;
        }
        
        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 180px;
            padding-bottom: 2px;
        }
        
        .footer {
            position: fixed;
            bottom: 15mm;
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
            font-size: 100pt;
            color: rgba(0, 0, 0, 0.03);
            z-index: -1;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        
        .catatan-box {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 5px;
        }
        
        .info-box {
            margin: 20px 0;
            padding: 12px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        ul {
            margin: 10px 0;
            padding-left: 30px;
        }
        
        ul li {
            margin-bottom: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body { 
                margin: 0; 
            }
            .footer { 
                position: fixed; 
            }
            .page-break {
                page-break-after: always;
            }
        }
        
        .tracking-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .tracking-table th,
        .tracking-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }
        
        .tracking-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- Watermark --}}
    <div class="watermark">{{ strtoupper($kepulangan->status) }}</div>
    
    {{-- Header --}}
    <div class="header">
        {{-- Logo (jika ada) --}}
        {{-- <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo" class="logo"> --}}
        
        <h1>Pondok Pesantren Al-Hikmah</h1>
        <h2>Yayasan Pendidikan Islam</h2>
        <p>Jl. Raya Pesantren No. 123, Jakarta Selatan 12345</p>
        <p>Telp: (021) 123456 | Email: info@alhikmah.ac.id | Website: www.alhikmah.ac.id</p>
    </div>
    
    {{-- Title --}}
    <div class="title">
        Surat Izin Kepulangan Santri
    </div>
    
    {{-- Nomor Surat --}}
    <div class="nomor">
        Nomor: {{ $kepulangan->id_kepulangan }}/IZIN-PULANG/PP-AH/{{ $kepulangan->tanggal_pulang->format('m') }}/{{ $kepulangan->tanggal_pulang->year }}
    </div>
    
    {{-- Content --}}
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Pengurus Pondok Pesantren Al-Hikmah, dengan ini menerangkan bahwa:</p>
        
        <div class="data-santri">
            <div class="data-row">
                <div class="data-label">Nama Lengkap</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $santri->nama_lengkap }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Nomor Induk Santri (NIS)</div>
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
                <div class="data-value">{{ $santri->alamat_santri ?? $santri->daerah_asal ?? 'Tidak tercatat' }}</div>
            </div>
        </div>
        
        <p>Adalah benar-benar santri aktif di Pondok Pesantren Al-Hikmah dan telah mendapat izin untuk pulang ke rumah dengan keterangan sebagai berikut:</p>
        
        <div class="data-santri">
            <div class="data-row">
                <div class="data-label">Tanggal Pengajuan Izin</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->tanggal_izin->format('d F Y') }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Tanggal Pulang</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->tanggal_pulang->format('d F Y') }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Tanggal Wajib Kembali</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->tanggal_kembali->format('d F Y') }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Durasi Izin</div>
                <div class="data-separator">:</div>
                <div class="data-value">{{ $kepulangan->durasi_izin }} hari</div>
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
        
        <p>Demikian surat izin ini dibuat dengan sebenarnya untuk dapat digunakan sebagaimana mestinya. Santri yang bersangkutan <strong>WAJIB</strong> kembali ke pesantren pada tanggal yang telah ditentukan di atas.</p>
        
        <div class="info-box">
            <p style="margin: 0;"><strong>⚠️ KETENTUAN PENTING:</strong></p>
            <ul style="margin: 10px 0 0 0;">
                <li>Santri wajib kembali sesuai tanggal yang tertera dalam surat ini</li>
                <li>Apabila terlambat kembali tanpa pemberitahuan yang sah, akan dikenakan sanksi sesuai peraturan pesantren</li>
                <li>Surat ini harus dibawa dan ditunjukkan kepada petugas jaga saat keluar dan masuk pesantren</li>
                <li>Orang tua/wali santri bertanggung jawab penuh atas keselamatan santri selama berada di rumah</li>
                <li>Harap menghubungi pihak pesantren jika terjadi kondisi darurat yang mengharuskan perpanjangan waktu kepulangan</li>
            </ul>
        </div>
    </div>
    
    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-left">
                <p>Mengetahui,<br>Wali Santri</p>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $santri->nama_orang_tua ?? '( ........................... )' }}</div>
            </div>
            <div class="signature-right">
                <p>Jakarta, {{ $tanggalCetak }}</p>
                <p>Pengurus Pondok Pesantren<br>Bidang Kesiswaan</p>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $kepulangan->approved_by ?? 'Admin' }}</div>
            </div>
        </div>
    </div>
    
    {{-- Footer --}}
    <div class="footer">
        <p>
            Dicetak pada: {{ $tanggalCetak }} | 
            ID: {{ $kepulangan->id_kepulangan }} | 
            Status: {{ $kepulangan->status }} | 
            Surat ini sah tanpa tanda tangan basah (Digital Signature)
        </p>
    </div>
    
    {{-- PAGE BREAK untuk halaman kedua (Lembar Tracking) --}}
    <div class="page-break"></div>
    
    {{-- Halaman 2: Lembar Tracking & Arsip --}}
    <div class="header">
        <h1>Pondok Pesantren Al-Hikmah</h1>
        <h2>Lembar Tracking Kepulangan Santri</h2>
        <p style="font-style: italic; color: #666;">(Lembar Arsip Internal - Tidak untuk diserahkan kepada santri)</p>
    </div>
    
    <div class="title" style="font-size: 12pt;">
        Tracking ID: {{ $kepulangan->id_kepulangan }}
    </div>
    
    <div class="content">
        <div class="catatan-box">
            <p style="margin: 0 0 10px 0;"><strong>📋 Data Kepulangan</strong></p>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px; width: 180px;"><strong>ID Kepulangan</strong></td>
                    <td style="padding: 5px;">: {{ $kepulangan->id_kepulangan }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>Nama Santri</strong></td>
                    <td style="padding: 5px;">: {{ $santri->nama_lengkap }} ({{ $santri->id_santri }})</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>Kelas</strong></td>
                    <td style="padding: 5px;">: {{ $santri->kelas }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>No. HP Wali</strong></td>
                    <td style="padding: 5px;">: {{ $santri->no_hp_wali ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>Periode Izin</strong></td>
                    <td style="padding: 5px;">: {{ $kepulangan->tanggal_pulang_formatted }} s/d {{ $kepulangan->tanggal_kembali_formatted }} ({{ $kepulangan->durasi_izin }} hari)</td>
                </tr>
                <tr>
                    <td style="padding: 5px; vertical-align: top;"><strong>Alasan</strong></td>
                    <td style="padding: 5px;">: {{ $kepulangan->alasan }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>Disetujui Oleh</strong></td>
                    <td style="padding: 5px;">: {{ $kepulangan->approved_by }} - {{ $kepulangan->approved_at_formatted }}</td>
                </tr>
            </table>
        </div>
        
        <h3 style="margin: 30px 0 15px 0; font-size: 12pt;">📊 Tracking Kepulangan</h3>
        <table class="tracking-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Status</th>
                    <th style="width: 25%;">Tanggal/Waktu</th>
                    <th style="width: 30%;">Keterangan</th>
                    <th style="width: 20%;">Petugas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Diajukan</td>
                    <td>{{ $kepulangan->tanggal_izin_formatted }}</td>
                    <td>Pengajuan izin kepulangan</td>
                    <td>Santri</td>
                </tr>
                <tr>
                    <td>{{ $kepulangan->status }}</td>
                    <td>{{ $kepulangan->approved_at_formatted }}</td>
                    <td>{{ $kepulangan->status == 'Disetujui' ? 'Izin disetujui dan surat dicetak' : 'Status diubah' }}</td>
                    <td>{{ $kepulangan->approved_by }}</td>
                </tr>
                <tr>
                    <td>Keluar Pesantren</td>
                    <td>_______________________</td>
                    <td>Santri keluar dari pesantren</td>
                    <td>_____________________</td>
                </tr>
                <tr>
                    <td>Kembali ke Pesantren</td>
                    <td>_______________________</td>
                    <td>Santri kembali ke pesantren</td>
                    <td>_____________________</td>
                </tr>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
            <p style="margin: 0 0 10px 0;"><strong>📞 Kontak Darurat:</strong></p>
            <table style="width: 100%; font-size: 10pt;">
                <tr>
                    <td style="padding: 3px;"><strong>Pesantren (Kantor)</strong></td>
                    <td style="padding: 3px;">: (021) 123456</td>
                    <td style="padding: 3px;"><strong>Keamanan 24 Jam</strong></td>
                    <td style="padding: 3px;">: (021) 123457</td>
                </tr>
                <tr>
                    <td style="padding: 3px;"><strong>Pengurus Putra</strong></td>
                    <td style="padding: 3px;">: 0812-3456-7890</td>
                    <td style="padding: 3px;"><strong>Pengurus Putri</strong></td>
                    <td style="padding: 3px;">: 0812-3456-7891</td>
                </tr>
                <tr>
                    <td style="padding: 3px;"><strong>Wali Santri</strong></td>
                    <td style="padding: 3px;">: {{ $santri->no_hp_wali ?? 'Belum ada data' }}</td>
                    <td style="padding: 3px;"><strong>Email Pesantren</strong></td>
                    <td style="padding: 3px;">: info@alhikmah.ac.id</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top: 20px; padding: 12px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <p style="margin: 0; font-size: 10pt;"><strong>⚠️ Catatan untuk Petugas:</strong></p>
            <ul style="margin: 5px 0 0 20px; font-size: 10pt;">
                <li>Pastikan santri membawa surat izin asli saat keluar</li>
                <li>Catat waktu keluar dan kembali dengan akurat</li>
                <li>Hubungi wali santri jika santri terlambat kembali lebih dari 2 jam</li>
                <li>Simpan lembar tracking ini untuk arsip administrasi</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p>
            Lembar Arsip Internal | Dicetak: {{ $tanggalCetak }} | 
            ID: {{ $kepulangan->id_kepulangan }} | 
            Halaman 2 dari 2
        </p>
    </div>
</body>
</html>