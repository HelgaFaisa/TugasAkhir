<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@page {
    margin: 0;
    sheet-size: 54mm 85.6mm;
}
* { margin:0; padding:0; box-sizing:border-box; }
html, body {
    margin:0; padding:0;
    width:54mm;
    font-family: Arial, Helvetica, sans-serif;
    background:#0b1a2e;
}

/*
 * LAYOUT: 1 tabel utama, height per row = fixed
 * Total: 17 + 40 + 1.6 + 9 + 9 + 9 = 85.6mm
 *
 * FOTO BULAT: pakai SVG <clipPath> + <image>
 * Ini SATU-SATUNYA cara yang 100% bekerja di mPDF
 * untuk crop gambar menjadi lingkaran.
 */

.main {
    width: 54mm;
    height: 85.6mm;
    border-collapse: collapse;
    background: #0b1a2e;
}

/* ── HEADER 17mm ── */
.td-header {
    height: 17mm;
    background: #0d1f3c;
    border-top: 0.8mm solid #c9a227;
    border-bottom: 0.5mm solid #c9a227;
    text-align: center;
    vertical-align: middle;
    padding: 1.5mm 2mm;
}
.h-sub {
    display:block; font-size:3.5pt; color:#a89060;
    letter-spacing:0.6pt; text-transform:uppercase; margin-bottom:1mm;
}
.h-title {
    display:block; font-size:11pt; font-weight:bold;
    color:#f0d060; letter-spacing:3.5pt; font-family:Georgia,serif;
    margin-bottom:0.5mm;
}
.h-loc {
    display:block; font-size:3pt; color:rgba(201,162,39,0.6);
    letter-spacing:0.5pt; text-transform:uppercase;
}

/* ── FOTO 40mm ── */
.td-foto {
    height: 40mm;
    background: #0b1a2e;
    text-align: center;
    vertical-align: middle;
    padding: 0;
}

/* ── DIVIDER 1.6mm ── */
.td-divider {
    height: 1.6mm;
    background: #0b1a2e;
    text-align: center;
    vertical-align: middle;
    padding: 0;
}
.div-line {
    display:inline-block;
    width:32mm; height:0.3mm;
    background:rgba(201,162,39,0.5);
}

/* ── NAMA 9mm ── */
.td-nama {
    height: 9mm;
    background: #0b1a2e;
    vertical-align: middle;
    text-align: center;
    padding: 0.5mm 3mm;
}
.nama-box {
    background: #0d1f3c;
    border: 0.5mm solid #c9a227;
    border-radius: 1mm;
    text-align: center;
    padding: 1.8mm 2mm;
    width: 100%;
    display: block;
}
.nama-text {
    font-size:8pt; font-weight:bold; color:#fff;
    letter-spacing:0.5pt; font-family:Georgia,serif;
    text-align: center;
    display: block;
    width: 100%;
}

/* ── INFO 9mm ── */
.td-info {
    height: 9mm;
    background: #0b1a2e;
    vertical-align: middle;
    padding: 1mm 3mm 0 3mm;
}
.info-t {
    width:48mm; height:8mm;
    border-collapse:collapse;
    border:0.4mm solid rgba(201,162,39,0.7);
    background:#0d1f3c;
}
.info-t td {
    height:8mm; padding:0 1.5mm; vertical-align:middle;
}
.ic-nis   { width:30%; }
.ic-kelas { width:37%; border-left:0.4mm solid rgba(201,162,39,0.5); border-right:0.4mm solid rgba(201,162,39,0.5); }
.ic-rfid  { width:33%; }
.lbl  { display:block; font-size:3pt; color:#c9a227; font-weight:bold; letter-spacing:0.3pt; text-transform:uppercase; margin-bottom:0.5mm; }
.val      { display:block; font-size:5.5pt; color:#fff; font-weight:bold; }
.val-sm   { display:block; font-size:4.5pt; color:#fff; font-weight:bold; }
.val-rfid { display:block; font-size:3.5pt; color:#90c4f0; font-weight:bold; font-family:monospace; word-break:break-all; }

/* ── BOTTOM 9mm ── */
.td-bottom {
    height: 9mm;
    background: #060f1c;
    border-top: 0.5mm solid #c9a227;
    text-align: center;
    vertical-align: middle;
    padding: 0;
}
.bot-text {
    font-size:2.8pt; color:#7a6020;
    letter-spacing:0.4pt; text-transform:uppercase;
}
</style>
</head>
<body>

<table class="main">

    {{-- HEADER --}}
    <tr><td class="td-header">
        <span class="h-sub">Pesantren Riyadlul Jannah &bull; Kab. Mojokerto</span>
        <span class="h-title">KARTU SANTRI</span>
        <span class="h-loc">PKPPS Riyadlul Jannah</span>
    </td></tr>

    {{-- FOTO: SVG dengan clipPath lingkaran --}}
    <tr><td class="td-foto">
        <svg xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink"
             width="34mm" height="34mm"
             viewBox="0 0 100 100"
             style="display:block;margin:0 auto;">
            <defs>
                <clipPath id="cp">
                    <circle cx="50" cy="50" r="44"/>
                </clipPath>
            </defs>

            {{-- Background --}}
            <circle cx="50" cy="50" r="44" fill="#1a2f4a"/>

            @if($fotoBase64 !== '')
            {{-- Foto: x=6,y=6 supaya pas dalam circle r=44 → lebar=88 --}}
            <image x="6" y="6" width="88" height="88"
                   clip-path="url(#cp)"
                   preserveAspectRatio="xMidYMid slice"
                   xlink:href="data:{{ $fotoMime }};base64,{{ $fotoBase64 }}"
                   href="data:{{ $fotoMime }};base64,{{ $fotoBase64 }}"/>
            @else
            <text x="50" y="50" text-anchor="middle" dominant-baseline="central"
                  font-size="36" font-weight="bold" fill="#c9a227"
                  font-family="Georgia">{{ $initial }}</text>
            @endif

            {{-- Ring emas luar --}}
            <circle cx="50" cy="50" r="48" fill="none" stroke="#c9a227" stroke-width="3.5"/>
            {{-- Ring tipis dalam --}}
            <circle cx="50" cy="50" r="44" fill="none" stroke="#8b6914" stroke-width="1"/>
        </svg>
    </td></tr>

    {{-- DIVIDER --}}
    <tr><td class="td-divider">
        <div class="div-line"></div>
    </td></tr>

    {{-- NAMA --}}
    <tr><td class="td-nama">
        <div class="nama-box">
            <span class="nama-text">{{ $namaSantri }}</span>
        </div>
    </td></tr>

    {{-- INFO --}}
    <tr><td class="td-info">
        <table class="info-t">
            <tr>
                <td class="ic-nis">
                    <span class="lbl">NIS</span>
                    <span class="val">{{ $nis }}</span>
                </td>
                <td class="ic-kelas">
                    <span class="lbl">Kelas</span>
                    <span class="val-sm">{{ $kelasNama }}</span>
                </td>
                <td class="ic-rfid">
                    <span class="lbl">UID RFID</span>
                    <span class="val-rfid">{{ $uid }}</span>
                </td>
            </tr>
        </table>
    </td></tr>

    {{-- BOTTOM --}}
    <tr><td class="td-bottom">
        <span class="bot-text">Pesantren Riyadlul Jannah &bull; Sistem Informasi Santri</span>
    </td></tr>

</table>
</body>
</html>