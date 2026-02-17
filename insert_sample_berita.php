<?php
/**
 * INSERT SAMPLE BERITA - Quick Setup
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'db_sim_pkpps';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>📝 INSERT SAMPLE BERITA</h1>";
    echo "<hr>";
    
    // Cek apakah sudah ada berita
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($count > 0) {
        echo "<p style='color: orange;'>⚠️ Sudah ada {$count} berita di database.</p>";
        echo "<p>Apakah Anda ingin menambah berita sample lagi?</p>";
        echo "<form method='post'>";
        echo "<button type='submit' name='tambah' style='padding: 10px 20px; font-size: 16px;'>Ya, Tambah Sample Berita</button>";
        echo "</form>";
        
        if (!isset($_POST['tambah'])) {
            exit;
        }
    }
    
    echo "<h2>🚀 Menambahkan sample berita...</h2>";
    
    // Ambil santri untuk pivot table
    $stmt = $pdo->query("SELECT id_santri FROM santris WHERE status = 'Aktif' LIMIT 3");
    $santriList = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($santriList) < 1) {
        die("<p style='color: red;'>❌ Tidak ada santri aktif! Tidak bisa buat sample berita 'santri_tertentu'</p>");
    }
    
    // 1. Berita untuk SEMUA
    $sql = "INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'published', 'semua', NOW(), NOW())";
    
    $beritaSemua = [
        ['B101', 'Pengumuman Libur Pondok', 'Assalamualaikum. Pondok akan libur tanggal 10-15 Februari 2026. Harap kembali tanggal 16 Februari jam 07:00. Barakallah.', 'Admin Pondok'],
        ['B102', 'Jadwal Ujian Semester', 'Kepada seluruh santri, ujian semester akan dimulai 20 Februari 2026. Silakan persiapkan diri dengan baik. Semoga sukses!', 'Bagian Pendidikan'],
    ];
    
    foreach ($beritaSemua as $data) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo "✅ Berita {$data[0]} (target: SEMUA) berhasil ditambahkan<br>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠️ Berita {$data[0]} sudah ada (skip)<br>";
            } else {
                echo "❌ Error: {$e->getMessage()}<br>";
            }
        }
    }
    
    echo "<br>";
    
    // 2. Berita untuk KELAS TERTENTU
    $sql = "INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, target_kelas, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'published', 'kelas_tertentu', ?, NOW(), NOW())";
    
    $beritaKelas = [
        ['B103', 'Info Kelas PB', 'Kepada santri kelas PB, akan ada kelas tambahan setiap Kamis jam 15:00. Mohon hadir tepat waktu.', 'Ustadz Nahwu', '["PB"]'],
        ['B104', 'Ujian Kelas Lambatan', 'Santri kelas Lambatan akan ujian kenaikan tingkat tanggal 5 Maret 2026. Harap persiapkan diri!', 'Bagian Pendidikan', '["Lambatan"]'],
        ['B105', 'Kegiatan Kelas Cepatan', 'Kelas Cepatan akan muhadhoroh setiap Jumat malam. Jadwal akan dibagikan minggu depan.', 'Ustadz Pembimbing', '["Cepatan"]'],
    ];
    
    foreach ($beritaKelas as $data) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo "✅ Berita {$data[0]} (target: KELAS) berhasil ditambahkan<br>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠️ Berita {$data[0]} sudah ada (skip)<br>";
            } else {
                echo "❌ Error: {$e->getMessage()}<br>";
            }
        }
    }
    
    echo "<br>";
    
    // 3. Berita untuk SANTRI TERTENTU
    $sql = "INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'published', 'santri_tertentu', NOW(), NOW())";
    
    $beritaSantri = [
        ['B106', 'Pesan Khusus - Menemui Admin', 'Assalamualaikum. Saudara diminta menemui bagian administrasi hari Senin jam 10:00. Terima kasih.', 'Admin'],
        ['B107', 'Reminder Uang Saku', 'Saldo uang saku Anda menipis (di bawah Rp 50.000). Harap segera top up. Barakallah.', 'Bagian Keuangan'],
    ];
    
    foreach ($beritaSantri as $data) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo "✅ Berita {$data[0]} (target: SANTRI TERTENTU) berhasil ditambahkan<br>";
            
            // Insert ke pivot table untuk santri pertama
            if (count($santriList) > 0) {
                $sqlPivot = "INSERT INTO berita_santri (id_berita, id_santri, sudah_dibaca, created_at, updated_at)
                             VALUES (?, ?, FALSE, NOW(), NOW())";
                $stmtPivot = $pdo->prepare($sqlPivot);
                $stmtPivot->execute([$data[0], $santriList[0]]);
                echo "  └─ Ditambahkan untuk santri {$santriList[0]}<br>";
            }
            
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠️ Berita {$data[0]} sudah ada (skip)<br>";
            } else {
                echo "❌ Error: {$e->getMessage()}<br>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h2>✅ SELESAI!</h2>";
    
    // Tampilkan ringkasan
    $stmt = $pdo->query("SELECT target_berita, COUNT(*) as jumlah FROM berita GROUP BY target_berita");
    $summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📊 Ringkasan Berita:</h3>";
    foreach ($summary as $row) {
        echo "- <strong>{$row['target_berita']}</strong>: {$row['jumlah']} berita<br>";
    }
    
    echo "<br><br>";
    echo "<a href='test_query_berita.php' style='padding: 10px 20px; background: #7C3AED; color: white; text-decoration: none; border-radius: 5px;'>Test Query Berita</a> ";
    echo "<a href='test_api_berita.php' style='padding: 10px 20px; background: #059669; color: white; text-decoration: none; border-radius: 5px;'>Debug API Berita</a>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>❌ ERROR</h1>";
    echo "<p>{$e->getMessage()}</p>";
}
?>
