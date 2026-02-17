<?php
/**
 * TEST API BERITA - Debugging
 * Cek apakah berita bisa diambil dengan benar untuk 3 kategori:
 * 1. Semua santri
 * 2. Kelas tertentu
 * 3. Santri tertentu
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
    
    echo "<h1>🔍 TEST API BERITA - DEBUGGING</h1>";
    echo "<hr>";
    
    // 1. CEK DATA BERITA
    echo "<h2>📰 1. DATA BERITA DI DATABASE</h2>";
    $stmt = $pdo->query("
        SELECT id, id_berita, judul, target_berita, target_kelas, status, created_at 
        FROM berita 
        ORDER BY created_at DESC
    ");
    $beritaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($beritaList)) {
        echo "<p style='color: red;'>❌ TIDAK ADA BERITA DI DATABASE!</p>";
    } else {
        echo "<p style='color: green;'>✅ Total berita: " . count($beritaList) . "</p>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>
                <th>ID</th>
                <th>Judul</th>
                <th>Target</th>
                <th>Target Kelas</th>
                <th>Status</th>
                <th>Tanggal</th>
              </tr>";
        
        foreach ($beritaList as $berita) {
            $targetKelas = $berita['target_kelas'] ?: '-';
            $status = $berita['status'];
            $color = $status === 'published' ? 'green' : 'orange';
            
            echo "<tr>";
            echo "<td>{$berita['id_berita']}</td>";
            echo "<td>{$berita['judul']}</td>";
            echo "<td><strong>{$berita['target_berita']}</strong></td>";
            echo "<td>{$targetKelas}</td>";
            echo "<td style='color: {$color};'><strong>{$status}</strong></td>";
            echo "<td>{$berita['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    
    // 2. CEK DATA SANTRI
    echo "<h2>👨‍🎓 2. DATA SANTRI (SAMPLE)</h2>";
    $stmt = $pdo->query("
        SELECT id_santri, nama_lengkap, kelas, status 
        FROM santris 
        WHERE status = 'Aktif'
        LIMIT 5
    ");
    $santriList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($santriList)) {
        echo "<p style='color: red;'>❌ TIDAK ADA SANTRI AKTIF!</p>";
    } else {
        echo "<p style='color: green;'>✅ Sample santri aktif:</p>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>
                <th>ID Santri</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Status</th>
              </tr>";
        
        foreach ($santriList as $santri) {
            echo "<tr>";
            echo "<td>{$santri['id_santri']}</td>";
            echo "<td>{$santri['nama_lengkap']}</td>";
            echo "<td><strong>{$santri['kelas']}</strong></td>";
            echo "<td>{$santri['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    
    // 3. CEK PIVOT TABLE berita_santri
    echo "<h2>🔗 3. PIVOT TABLE (berita_santri)</h2>";
    $stmt = $pdo->query("
        SELECT bs.*, b.judul, s.nama_lengkap
        FROM berita_santri bs
        LEFT JOIN berita b ON bs.id_berita = b.id_berita
        LEFT JOIN santris s ON bs.id_santri = s.id_santri
        LIMIT 10
    ");
    $pivotList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($pivotList)) {
        echo "<p style='color: orange;'>⚠️ PIVOT TABLE KOSONG (Normal untuk berita 'semua' dan 'kelas_tertentu')</p>";
    } else {
        echo "<p style='color: green;'>✅ Total relasi: " . count($pivotList) . "</p>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>
                <th>ID Berita</th>
                <th>Judul Berita</th>
                <th>ID Santri</th>
                <th>Nama Santri</th>
                <th>Sudah Dibaca</th>
              </tr>";
        
        foreach ($pivotList as $pivot) {
            $dibaca = $pivot['sudah_dibaca'] ? '✅ Ya' : '❌ Belum';
            echo "<tr>";
            echo "<td>{$pivot['id_berita']}</td>";
            echo "<td>{$pivot['judul']}</td>";
            echo "<td>{$pivot['id_santri']}</td>";
            echo "<td>{$pivot['nama_lengkap']}</td>";
            echo "<td>{$dibaca}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    
    // 4. CEK DATA USER (WALI)
    echo "<h2>👤 4. DATA USER/WALI (SAMPLE)</h2>";
    $stmt = $pdo->query("
        SELECT id, username, role, role_id 
        FROM users 
        WHERE role = 'wali'
        LIMIT 5
    ");
    $userList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($userList)) {
        echo "<p style='color: red;'>❌ TIDAK ADA USER WALI!</p>";
    } else {
        echo "<p style='color: green;'>✅ Sample user wali:</p>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Role ID (id_santri)</th>
              </tr>";
        
        foreach ($userList as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td><strong>{$user['role_id']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    
    // 5. SIMULASI FILTER BERITA UNTUK SANTRI TERTENTU
    if (!empty($santriList)) {
        $sampleSantri = $santriList[0];
        $idSantri = $sampleSantri['id_santri'];
        $kelasSantri = $sampleSantri['kelas'];
        
        echo "<h2>🎯 5. SIMULASI FILTER BERITA</h2>";
        echo "<p><strong>Untuk Santri:</strong> {$sampleSantri['nama_lengkap']} (ID: {$idSantri}, Kelas: {$kelasSantri})</p>";
        
        // Query sesuai dengan logic di ApiBeritaController
        $sql = "
            SELECT 
                b.id,
                b.id_berita,
                b.judul,
                b.target_berita,
                b.target_kelas,
                b.status
            FROM berita b
            WHERE b.status = 'published'
            AND (
                -- 1. Berita untuk SEMUA
                b.target_berita = 'semua'
                
                -- 2. Berita untuk KELAS TERTENTU
                OR (
                    b.target_berita = 'kelas_tertentu'
                    AND JSON_CONTAINS(b.target_kelas, '\"$kelasSantri\"')
                )
                
                -- 3. Berita untuk SANTRI TERTENTU
                OR (
                    b.target_berita = 'santri_tertentu'
                    AND EXISTS (
                        SELECT 1 FROM berita_santri bs
                        WHERE bs.id_berita = b.id_berita
                        AND bs.id_santri = '$idSantri'
                    )
                )
            )
            ORDER BY b.created_at DESC
        ";
        
        $stmt = $pdo->query($sql);
        $filteredBerita = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($filteredBerita)) {
            echo "<p style='color: red;'>❌ TIDAK ADA BERITA UNTUK SANTRI INI!</p>";
            echo "<p>💡 <strong>Solusi:</strong></p>";
            echo "<ul>";
            echo "<li>Buat berita dengan target 'Semua Santri', atau</li>";
            echo "<li>Buat berita dengan target 'Kelas: {$kelasSantri}', atau</li>";
            echo "<li>Buat berita dengan target 'Santri Tertentu' dan pilih santri ini</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: green;'>✅ Santri ini berhak melihat <strong>" . count($filteredBerita) . " berita</strong></p>";
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
            echo "<tr style='background: #f0f0f0;'>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Target</th>
                    <th>Target Kelas</th>
                  </tr>";
            
            foreach ($filteredBerita as $berita) {
                $targetKelas = $berita['target_kelas'] ?: '-';
                echo "<tr>";
                echo "<td>{$berita['id_berita']}</td>";
                echo "<td>{$berita['judul']}</td>";
                echo "<td><strong>{$berita['target_berita']}</strong></td>";
                echo "<td>{$targetKelas}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<hr>";
    echo "<h3>✅ DEBUGGING SELESAI</h3>";
    echo "<p><strong>Langkah selanjutnya:</strong></p>";
    echo "<ol>";
    echo "<li>Pastikan ada berita dengan status 'published'</li>";
    echo "<li>Pastikan target_berita sesuai (semua/kelas_tertentu/santri_tertentu)</li>";
    echo "<li>Jika target 'kelas_tertentu', pastikan target_kelas berisi kelas yang benar dalam format JSON</li>";
    echo "<li>Jika target 'santri_tertentu', pastikan ada data di pivot table berita_santri</li>";
    echo "<li>Test login di mobile dengan user wali yang memiliki role_id sesuai dengan id_santri</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>❌ ERROR DATABASE</h1>";
    echo "<p>{$e->getMessage()}</p>";
}
?>
