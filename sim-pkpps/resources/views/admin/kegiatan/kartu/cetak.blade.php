<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu RFID 2 Sisi - {{ $santri->nama_lengkap ?? 'Preview' }}</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 54mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
        }

        /* === SISI DEPAN === */
        .card-front {
            width: 85.6mm;
            height: 54mm;
            background: #2C3E50;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        /* Background Elements */
        .bg-circle-1 {
            position: absolute;
            top: -20mm;
            right: -20mm;
            width: 70mm;
            height: 70mm;
            background: #6FBA9D;
            border-radius: 50%;
            opacity: 0.15;
        }

        .bg-circle-2 {
            position: absolute;
            bottom: -25mm;
            left: -15mm;
            width: 60mm;
            height: 60mm;
            background: #8FCAAE;
            border-radius: 50%;
            opacity: 0.12;
        }

        .wave {
            position: absolute;
            top: 0;
            right: 0;
            width: 45mm;
            height: 100%;
            background: linear-gradient(135deg, #6FBA9D 0%, #5EA98C 100%);
            clip-path: polygon(40% 0%, 100% 0%, 100% 100%, 0% 100%);
        }

        /* Header */
        .header {
            position: relative;
            z-index: 10;
            padding: 4mm 3.5mm;
            display: table;
            width: 100%;
        }

        .logo-cell {
            display: table-cell;
            width: 12mm;
            vertical-align: middle;
        }

        .logo {
            width: 10mm;
            height: 10mm;
            background: linear-gradient(135deg, #6FBA9D, #8FCAAE);
            border-radius: 50%;
            text-align: center;
            line-height: 10mm;
            font-size: 16pt;
            box-shadow: 0 2mm 4mm rgba(0,0,0,0.2);
        }

        .title-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 2mm;
        }

        .title-cell h1 {
            font-size: 11pt;
            font-weight: 900;
            color: white;
            letter-spacing: 1pt;
            text-shadow: 0 1mm 2mm rgba(0,0,0,0.2);
        }

        .title-cell p {
            font-size: 6pt;
            color: #6FBA9D;
            margin-top: 0.5mm;
            font-weight: 600;
        }

        /* Content Area */
        .content {
            position: relative;
            z-index: 10;
            padding: 0 3.5mm;
            display: table;
            width: 100%;
        }

        .photo-col {
            display: table-cell;
            width: 22mm;
            vertical-align: middle;
            text-align: center;
        }

        .photo-frame {
            width: 18mm;
            height: 18mm;
            background: white;
            border-radius: 50%;
            margin: 0 auto 2mm;
            padding: 1.5mm;
            box-shadow: 0 2mm 6mm rgba(0,0,0,0.25);
            position: relative;
        }

        .photo {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #6FBA9D, #8FCAAE);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20pt;
            color: white;
            font-weight: bold;
        }

        .badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 5mm;
            height: 5mm;
            background: #6FBA9D;
            border-radius: 50%;
            border: 1.5mm solid white;
            text-align: center;
            line-height: 5mm;
            color: white;
            font-size: 7pt;
            font-weight: bold;
        }

        .name {
            font-size: 7pt;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            text-shadow: 0 0.5mm 1mm rgba(0,0,0,0.3);
            line-height: 1.2;
        }

        .info-col {
            display: table-cell;
            vertical-align: middle;
            padding: 0 2mm;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 2mm;
            padding: 2.5mm 3mm;
            box-shadow: 0 1mm 4mm rgba(0,0,0,0.15);
        }

        .info-item {
            margin-bottom: 1.5mm;
            font-size: 6pt;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .label {
            font-weight: 800;
            color: #2C3E50;
            display: inline-block;
            width: 12mm;
            text-transform: uppercase;
        }

        .value {
            color: #34495E;
            font-weight: 600;
        }

        .rfid {
            font-family: 'Courier New', monospace;
            font-size: 5pt;
        }

        .qr-col {
            display: table-cell;
            width: 20mm;
            vertical-align: middle;
            text-align: center;
        }

        .qr-box {
            background: white;
            border-radius: 2mm;
            padding: 1.5mm;
            box-shadow: 0 2mm 5mm rgba(0,0,0,0.2);
            display: inline-block;
        }

        .qr-img {
            width: 16mm;
            height: 16mm;
            display: block;
        }

        .scan {
            margin-top: 1.5mm;
            font-size: 5pt;
            color: white;
            font-weight: 700;
            background: linear-gradient(135deg, #6FBA9D, #5EA98C);
            padding: 1mm 2mm;
            border-radius: 1mm;
            display: inline-block;
            text-transform: uppercase;
        }

        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1.5mm;
            background: linear-gradient(90deg, #6FBA9D 0%, #5EA98C 50%, #6FBA9D 100%);
            z-index: 10;
        }

        /* === SISI BELAKANG === */
        .card-back {
            width: 85.6mm;
            height: 54mm;
            background: linear-gradient(135deg, #6FBA9D 0%, #5EA98C 100%);
            position: relative;
            overflow: hidden;
        }

        .back-pattern-1 {
            position: absolute;
            top: -15mm;
            left: -15mm;
            width: 50mm;
            height: 50mm;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .back-pattern-2 {
            position: absolute;
            bottom: -20mm;
            right: -20mm;
            width: 60mm;
            height: 60mm;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .back-content {
            position: relative;
            z-index: 5;
            padding: 5mm;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .back-header {
            text-align: center;
            margin-bottom: 3mm;
        }

        .back-logo {
            width: 12mm;
            height: 12mm;
            background: white;
            border-radius: 50%;
            margin: 0 auto 2mm;
            text-align: center;
            line-height: 12mm;
            font-size: 20pt;
            box-shadow: 0 2mm 6mm rgba(0,0,0,0.15);
        }

        .back-title {
            font-size: 10pt;
            font-weight: 900;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }

        .back-subtitle {
            font-size: 7pt;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 0.5mm;
        }

        .info-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 2mm;
            padding: 3mm;
            margin-bottom: 2mm;
            flex-grow: 1;
        }

        .section-title {
            font-size: 7pt;
            font-weight: 800;
            color: #2C3E50;
            text-transform: uppercase;
            margin-bottom: 2mm;
            border-bottom: 0.5mm solid #6FBA9D;
            padding-bottom: 1mm;
        }

        .rule {
            font-size: 5.5pt;
            color: #34495E;
            line-height: 1.4;
            margin-bottom: 1.5mm;
            display: flex;
        }

        .rule-icon {
            color: #6FBA9D;
            margin-right: 1.5mm;
            font-weight: bold;
        }

        .contact {
            background: white;
            border-radius: 2mm;
            padding: 2mm 3mm;
            text-align: center;
            font-size: 5pt;
            color: #2C3E50;
        }

        .contact-item {
            margin-bottom: 0.5mm;
            font-weight: 600;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-icon {
            color: #6FBA9D;
            margin-right: 1mm;
        }
    </style>
</head>
<body>
    <!-- ========== SISI DEPAN ========== -->
    <div class="card-front">
        <div class="bg-circle-1"></div>
        <div class="bg-circle-2"></div>
        <div class="wave"></div>

        <div class="header">
            <div class="logo-cell">
                <div class="logo">🕌</div>
            </div>
            <div class="title-cell">
                <h1>PKPPS</h1>
                <p>Pesantren Riyadlul Jannah</p>
            </div>
        </div>

        <div class="content">
            <div class="photo-col">
                <div class="photo-frame">
                    <div class="photo">
                        @if(isset($santri)){{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}@else A @endif
                    </div>
                    <div class="badge">✓</div>
                </div>
                <div class="name">
                    @if(isset($santri)){{ strtoupper(Str::limit($santri->nama_lengkap, 12, '')) }}@else YOUR NAME @endif
                </div>
            </div>

            <div class="info-col">
                <div class="info-box">
                    <div class="info-item">
                        <span class="label">ID</span>
                        <span class="value">: @if(isset($santri)){{ $santri->id_santri }}@else S004 @endif</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Kelas</span>
                        <span class="value">: @if(isset($santri)){{ $santri->kelas }}@else Lambatan @endif</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Status</span>
                        <span class="value">: @if(isset($santri)){{ $santri->status }}@else Aktif @endif</span>
                    </div>
                    <div class="info-item">
                        <span class="label">RFID</span>
                        <span class="value rfid">: @if(isset($santri) && $santri->rfid_uid){{ Str::limit($santri->rfid_uid, 14) }}@else 04A3B62FD9C1 @endif</span>
                    </div>
                </div>
            </div>

            <div class="qr-col">
                <div class="qr-box">
                    @if(isset($santri) && $santri->rfid_uid)
                        <img src="https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl={{ urlencode($santri->rfid_uid) }}" class="qr-img" alt="QR">
                    @else
                        <img src="https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=04A3B62FD9C1" class="qr-img" alt="QR">
                    @endif
                </div>
                <div class="scan">SCAN ME</div>
            </div>
        </div>

        <div class="footer"></div>
    </div>

    <!-- ========== SISI BELAKANG ========== -->
    <div class="card-back">
        <div class="back-pattern-1"></div>
        <div class="back-pattern-2"></div>

        <div class="back-content">
            <div class="back-header">
                <div class="back-logo">🕌</div>
                <div class="back-title">PKPPS</div>
                <div class="back-subtitle">Pesantren Riyadlul Jannah</div>
            </div>

            <div class="info-section">
                <div class="section-title">📋 Ketentuan Kartu</div>
                <div class="rule">
                    <span class="rule-icon">✓</span>
                    <span>Kartu ini adalah identitas resmi santri PKPPS</span>
                </div>
                <div class="rule">
                    <span class="rule-icon">✓</span>
                    <span>Wajib dibawa setiap saat di lingkungan pesantren</span>
                </div>
                <div class="rule">
                    <span class="rule-icon">✓</span>
                    <span>Digunakan untuk absensi kegiatan dan akses fasilitas</span>
                </div>
                <div class="rule">
                    <span class="rule-icon">✓</span>
                    <span>Jika hilang segera lapor ke bagian administrasi</span>
                </div>
                <div class="rule">
                    <span class="rule-icon">✓</span>
                    <span>Harap dijaga dan tidak dipinjamkan</span>
                </div>
            </div>

            <div class="contact">
                <div class="contact-item">
                    <span class="contact-icon">📍</span> Jl. Pesantren No. 123, Yogyakarta
                </div>
                <div class="contact-item">
                    <span class="contact-icon">📞</span> (0274) 123-4567
                </div>
                <div class="contact-item">
                    <span class="contact-icon">📧</span> admin@pkpps.ac.id
                </div>
            </div>
        </div>
    </div>
</body>
</html>