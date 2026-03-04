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
    overflow: hidden;
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
    overflow: hidden;
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
    overflow: hidden;
}
.nama-box {
    background: #0d1f3c;
    border: 0.5mm solid #c9a227;
    border-radius: 1mm;
    text-align: center;
    padding: 1.2mm 1.5mm;
    width: 100%;
    display: block;
    overflow: hidden;
    max-height: 7.5mm;
}
.nama-text {
    font-size:6pt; font-weight:bold; color:#fff;
    letter-spacing:0.3pt; font-family:Georgia,serif;
    text-align: center;
    display: block;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
}

/* ── INFO 9mm ── */
.td-info {
    height: 9mm;
    background: #0b1a2e;
    vertical-align: middle;
    padding: 1mm 3mm 0 3mm;
    overflow: hidden;
}
.info-t {
    width:48mm; height:8mm;
    border-collapse:collapse;
    border:0.4mm solid rgba(201,162,39,0.7);
    background:#0d1f3c;
}
.info-t td {
    height:8mm; padding:0 1.5mm; vertical-align:middle;
    overflow:hidden;
}
.ic-nis   { width:30%; }
.ic-kelas { width:37%; border-left:0.4mm solid rgba(201,162,39,0.5); border-right:0.4mm solid rgba(201,162,39,0.5); }
.ic-rfid  { width:33%; }
.lbl  { display:block; font-size:3pt; color:#c9a227; font-weight:bold; letter-spacing:0.3pt; text-transform:uppercase; margin-bottom:0.5mm; }
.val      { display:block; font-size:4.5pt; color:#fff; font-weight:bold; white-space:nowrap; overflow:hidden; }
.val-sm   { display:block; font-size:4pt; color:#fff; font-weight:bold; overflow:hidden; }
.val-rfid { display:block; font-size:3pt; color:#90c4f0; font-weight:bold; font-family:monospace; word-break:break-all; overflow:hidden; }

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

    {{-- FOTO: teknik "mask ring" — 100% kompatibel mPDF --}}
    <tr><td class="td-foto">
        <svg xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink"
             width="34mm" height="34mm"
             viewBox="0 0 100 100"
             overflow="hidden"
             style="display:block;margin:0 auto;">

            {{-- 1. Background bulat (terlihat jika tidak ada foto) --}}
            <circle cx="50" cy="50" r="37" fill="#1a2f4a"/>

            @if($fotoBase64 !== '')
            {{-- 2. Foto persegi biasa — sengaja lebih besar dari lingkaran --}}
            <image x="10" y="10" width="80" height="80"
                   preserveAspectRatio="xMidYMid slice"
                   xlink:href="data:{{ $fotoMime }};base64,{{ $fotoBase64 }}"
                   href="data:{{ $fotoMime }};base64,{{ $fotoBase64 }}"/>
            @else
            <text x="50" y="50" text-anchor="middle" dominant-baseline="central"
                  font-size="34" font-weight="bold" fill="#c9a227"
                  font-family="Georgia">{{ $initial }}</text>
            @endif

            {{-- 3. MASK RING — lingkaran tebal warna background menutupi
                 semua bagian foto di luar radius 37.
                 r=54 sw=34 → inner=54-17=37, outer=54+17=71 (sampai sudut) --}}
            <circle cx="50" cy="50" r="54" fill="none"
                    stroke="#0b1a2e" stroke-width="34"/>

            {{-- 4. Bingkai emas utama --}}
            <circle cx="50" cy="50" r="38.5" fill="none"
                    stroke="#c9a227" stroke-width="3"/>

            {{-- 5. Aksen tipis luar --}}
            <circle cx="50" cy="50" r="41" fill="none"
                    stroke="rgba(201,162,39,0.4)" stroke-width="0.6"/>

            {{-- 6. Aksen tipis dalam --}}
            <circle cx="50" cy="50" r="36" fill="none"
                    stroke="rgba(139,105,20,0.5)" stroke-width="0.5"/>
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