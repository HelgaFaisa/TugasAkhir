<?php
// Quick check: apakah file gambar benar-benar ada?

$imagePath = __DIR__ . '/sim-pkpps/storage/app/public/berita/cxPh4dGaR6qpyPeshxvSQAjgaY7xef9t180dgShU.jpg';
$publicPath = __DIR__ . '/sim-pkpps/public/storage/berita/cxPh4dGaR6qpyPeshxvSQAjgaY7xef9t180dgShU.jpg';

echo "<h1>🔍 Check Gambar Berita</h1>";

echo "<h2>1. File di storage/app/public/berita/</h2>";
if (file_exists($imagePath)) {
    echo "✅ File ADA di: <code>$imagePath</code><br>";
    echo "Ukuran: " . filesize($imagePath) . " bytes<br>";
} else {
    echo "❌ File TIDAK ADA di: <code>$imagePath</code><br>";
}

echo "<h2>2. File di public/storage/berita/ (symlink)</h2>";
if (file_exists($publicPath)) {
    echo "✅ File ACCESSIBLE di: <code>$publicPath</code><br>";
    echo "Ukuran: " . filesize($publicPath) . " bytes<br>";
} else {
    echo "❌ File TIDAK ACCESSIBLE di: <code>$publicPath</code><br>";
}

echo "<h2>3. Test URL</h2>";
$url = 'http://localhost/TugasAkhir/sim-pkpps/public/storage/berita/cxPh4dGaR6qpyPeshxvSQAjgaY7xef9t180dgShU.jpg';
echo "URL: <a href='$url' target='_blank'>$url</a><br><br>";

if (file_exists($publicPath)) {
    echo "<img src='$url' style='max-width: 400px; border: 2px solid green;' onerror='this.style.border=\"2px solid red\";'><br>";
    echo "<p>Jika gambar di atas tidak muncul, berarti ada masalah CORS atau server config.</p>";
}

echo "<h2>4. Symlink Check</h2>";
$symlinkPath = __DIR__ . '/sim-pkpps/public/storage';
if (is_link($symlinkPath)) {
    echo "✅ Symlink EXISTS<br>";
    echo "Target: " . readlink($symlinkPath) . "<br>";
} else if (is_dir($symlinkPath)) {
    echo "✅ Directory EXISTS (bukan symlink)<br>";
} else {
    echo "❌ Storage link TIDAK ADA!<br>";
    echo "<p><strong>Solusi:</strong> Jalankan <code>php artisan storage:link</code></p>";
}
?>
