<?php
// Test API Capaian
require_once __DIR__ . '/sim-pkpps/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/sim-pkpps/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test data
echo "=== TEST CAPAIAN API ===\n\n";

// 1. Check Santri
$santri = \App\Models\Santri::first();
if (!$santri) {
    echo "❌ Tidak ada santri di database\n";
    exit;
}
echo "✅ Santri: {$santri->nama_lengkap} (ID: {$santri->id_santri}, Kelas: {$santri->kelas})\n\n";

// 2. Check Semester
$semester = \App\Models\Semester::aktif()->first();
if (!$semester) {
    echo "⚠️  Tidak ada semester aktif\n";
} else {
    echo "✅ Semester Aktif: {$semester->nama_semester} (ID: {$semester->id_semester})\n\n";
}

// 3. Check Materi untuk kelas santri
$materi = \App\Models\Materi::where('kelas', $santri->kelas)->get();
echo "📚 Materi untuk kelas {$santri->kelas}: {$materi->count()} materi\n";
foreach ($materi as $m) {
    echo "   - {$m->nama_kitab} ({$m->kategori})\n";
}
echo "\n";

// 4. Check Capaian
$capaians = \App\Models\Capaian::where('id_santri', $santri->id_santri)
    ->with(['materi', 'semester'])
    ->get();
echo "📊 Capaian Santri: {$capaians->count()} capaian\n";
if ($capaians->isEmpty()) {
    echo "⚠️  Belum ada capaian untuk santri ini\n\n";
    echo "🔧 Membuat capaian sample...\n";
    
    // Create sample capaian if none exists
    foreach ($materi->take(3) as $m) {
        $capaian = \App\Models\Capaian::create([
            'id_santri' => $santri->id_santri,
            'id_materi' => $m->id_materi,
            'id_semester' => $semester?->id_semester,
            'halaman_selesai' => rand(10, 50),
            'tanggal_input' => now(),
        ]);
        echo "   ✅ Created capaian: {$m->nama_kitab} - {$capaian->persentase}%\n";
    }
    
    // Reload capaians
    $capaians = \App\Models\Capaian::where('id_santri', $santri->id_santri)
        ->with(['materi', 'semester'])
        ->get();
    echo "\n📊 Total capaian sekarang: {$capaians->count()}\n";
} else {
    foreach ($capaians as $c) {
        echo "   - {$c->materi->nama_kitab}: {$c->persentase}% ({$c->materi->kategori})\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
