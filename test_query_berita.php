<?php
/**
 * TEST QUERY BERITA - Cek SQL Query
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database config
$host = 'localhost';
$db = 'db_sim_pkpps';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🧪 TEST QUERY BERITA</h1>";
    echo "<hr>";
    
    // Ambil sample santri
    $stmt = $pdo->query("SELECT id_santri, nama_lengkap, kelas FROM santris WHERE status = 'Aktif' LIMIT 1");
    $santri = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$santri) {
        die("<p style='color: red;'>❌ Tidak ada santri aktif di database!</p>");
    }
    
    $idSantri = $santri['id_santri'];
    $kelasSantri = $santri['kelas'];
    
    echo "<h2>👨‍🎓 Testing dengan Santri:</h2>";
    echo "<p><strong>ID:</strong> {$idSantri}</p>";
    echo "<p><strong>Nama:</strong> {$santri['nama_lengkap']}</p>";
    echo "<p><strong>Kelas:</strong> {$kelasSantri}</p>";
    echo "<hr>";
    
    // Test Query 1: Berita untuk SEMUA
    echo "<h2>1️⃣ Berita untuk SEMUA SANTRI</h2>";
    $sql = "SELECT id_berita, judul FROM berita WHERE status = 'published' AND target_berita = 'semua'";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Jumlah: <strong>" . count($result) . "</strong></p>";
    foreach ($result as $row) {
        echo "- {$row['id_berita']}: {$row['judul']}<br>";
    }
    echo "<hr>";
    
    // Test Query 2: Berita untuk KELAS TERTENTU
    echo "<h2>2️⃣ Berita untuk KELAS TERTENTU (Kelas: {$kelasSantri})</h2>";
    $sql = "SELECT id_berita, judul, target_kelas 
            FROM berita 
            WHERE status = 'published' 
            AND target_berita = 'kelas_tertentu'
            AND JSON_CONTAINS(target_kelas, '\"{$kelasSantri}\"')";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Jumlah: <strong>" . count($result) . "</strong></p>";
    foreach ($result as $row) {
        echo "- {$row['id_berita']}: {$row['judul']} (Target Kelas: {$row['target_kelas']})<br>";
    }
    echo "<hr>";
    
    // Test Query 3: Berita untuk SANTRI TERTENTU (FIX VERSION)
    echo "<h2>3️⃣ Berita untuk SANTRI TERTENTU (ID: {$idSantri})</h2>";
    
    // Versi yang BENAR - menggunakan prepared statement untuk menghindari SQL injection
    $sql = "SELECT b.id_berita, b.judul
            FROM berita b
            WHERE b.status = 'published'
            AND b.target_berita = 'santri_tertentu'
            AND EXISTS (
                SELECT 1 
                FROM berita_santri bs
                WHERE bs.id_berita = b.id_berita
                AND bs.id_santri = :id_santri
            )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_santri' => $idSantri]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Jumlah: <strong>" . count($result) . "</strong></p>";
    foreach ($result as $row) {
        echo "- {$row['id_berita']}: {$row['judul']}<br>";
    }
    
    // Cek pivot table
    echo "<h3>📋 Data di Pivot Table untuk Santri ini:</h3>";
    $sql = "SELECT bs.id_berita, b.judul, bs.sudah_dibaca
            FROM berita_santri bs
            LEFT JOIN berita b ON bs.id_berita = b.id_berita
            WHERE bs.id_santri = :id_santri";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_santri' => $idSantri]);
    $pivot = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($pivot)) {
        echo "<p style='color: orange;'>⚠️ Tidak ada data di pivot table untuk santri ini</p>";
    } else {
        foreach ($pivot as $row) {
            $status = $row['sudah_dibaca'] ? '✅ Sudah dibaca' : '❌ Belum dibaca';
            echo "- {$row['id_berita']}: {$row['judul']} ({$status})<br>";
        }
    }
    
    echo "<hr>";
    
    // Test Query GABUNGAN (seperti di API)
    echo "<h2>🎯 QUERY GABUNGAN (Seperti di API)</h2>";
    $sql = "SELECT b.id_berita, b.judul, b.target_berita
            FROM berita b
            WHERE b.status = 'published'
            AND (
                -- SEMUA
                b.target_berita = 'semua'
                
                -- KELAS TERTENTU
                OR (
                    b.target_berita = 'kelas_tertentu'
                    AND JSON_CONTAINS(b.target_kelas, '\"{$kelasSantri}\"')
                )
                
                -- SANTRI TERTENTU
                OR (
                    b.target_berita = 'santri_tertentu'
                    AND EXISTS (
                        SELECT 1 
                        FROM berita_santri bs
                        WHERE bs.id_berita = b.id_berita
                        AND bs.id_santri = :id_santri
                    )
                )
            )
            ORDER BY b.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_santri' => $idSantri]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>TOTAL BERITA YANG BISA DILIHAT SANTRI INI: " . count($result) . "</strong></p>";
    
    if (empty($result)) {
        echo "<p style='color: red; font-size: 18px;'>❌ TIDAK ADA BERITA!</p>";
        echo "<h3>💡 Solusi:</h3>";
        echo "<ul>";
        echo "<li>Buat berita dengan target 'Semua Santri', atau</li>";
        echo "<li>Buat berita dengan target 'Kelas: {$kelasSantri}', atau</li>";
        echo "<li>Buat berita dengan target 'Santri Tertentu' dan tambahkan santri ini ke pivot table</li>";
        echo "</ul>";
    } else {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Judul</th><th>Target</th></tr>";
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>{$row['id_berita']}</td>";
            echo "<td>{$row['judul']}</td>";
            echo "<td><strong>{$row['target_berita']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h3>✅ TEST SELESAI</h3>";
    echo "<p>Jika query gabungan menampilkan berita, berarti API seharusnya juga akan menampilkan berita yang sama.</p>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>❌ ERROR</h1>";
    echo "<p>{$e->getMessage()}</p>";
}
?>
