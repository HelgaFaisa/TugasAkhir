<?php
// TEST URL GAMBAR - Cek format URL yang dihasilkan

header('Content-Type: application/json');

$baseUrl = 'http://localhost/TugasAkhir/sim-pkpps/public';
$gambarPath = 'berita/sample.jpg';

$hasil = [
    'asset_function' => $baseUrl . '/storage/' . $gambarPath,
    'url_function' => $baseUrl . '/storage/' . $gambarPath,
    'expected_mobile' => $baseUrl . '/storage/' . $gambarPath,
];

echo json_encode($hasil, JSON_PRETTY_PRINT);
