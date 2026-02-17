<?php
// add_capaian_test_data.php - Add sample capaian data with progress

require __DIR__ . '/sim-pkpps/vendor/autoload.php';
$app = require __DIR__ . '/sim-pkpps/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ADDING CAPAIAN TEST DATA ===\n\n";

$santri = \App\Models\Santri::where('id_santri', 'S001')->first();
$semester = \App\Models\Semester::aktif()->first();

if (!$santri || !$semester) {
    echo "❌ Missing santri or semester\n";
    exit;
}

echo "Santri: {$santri->nama_lengkap}\n";
echo "Semester: {$semester->nama_semester}\n\n";

// Get or create materis
$materis = \App\Models\Materi::where('kelas', $santri->kelas)->get();

if ($materis->isEmpty()) {
    echo "Creating sample materi...\n";
    
    $materiData = [
        [
            'nama_kitab' => 'Al-Baqarah',
            'kategori' => 'Al-Qur\'an',
            'kelas' => 'Lambatan',
            'halaman_mulai' => 1,
            'halaman_akhir' => 100,
        ],
        [
            'nama_kitab' => 'Shahih Bukhari Juz 1',
            'kategori' => 'Hadist',
            'kelas' => 'Lambatan',
            'halaman_mulai' => 1,
            'halaman_akhir' => 150,
        ],
        [
            'nama_kitab' => 'Tafsir Jalalain',
            'kategori' => 'Materi Tambahan',
            'kelas' => 'Lambatan',
            'halaman_mulai' => 1,
            'halaman_akhir' => 200,
        ],
    ];
    
    foreach ($materiData as $data) {
        $m = \App\Models\Materi::create($data);
        echo "  ✅ Created: {$m->nama_kitab}\n";
    }
    
    $materis = \App\Models\Materi::where('kelas', $santri->kelas)->get();
}

echo "\nAdding capaian with progress...\n";

// Delete existing capaians
\App\Models\Capaian::where('id_santri', $santri->id_santri)->delete();

foreach ($materis as $index => $materi) {
    // Create different progress levels
    $progressLevels = [
        ['halaman' => '1-15', 'persentase' => 15],
        ['halaman' => '1-25,30-40', 'persentase' => 40],
        ['halaman' => '1-50,60-80', 'persentase' => 70],
    ];
    
    $progress = $progressLevels[$index % 3];
    
    $capaian = \App\Models\Capaian::create([
        'id_santri' => $santri->id_santri,
        'id_materi' => $materi->id_materi,
        'id_semester' => $semester->id_semester,
        'halaman_selesai' => $progress['halaman'],
        'tanggal_input' => now(),
    ]);
    
    echo "  ✅ {$materi->nama_kitab}: {$capaian->persentase}%\n";
}

echo "\n=== DONE ===\n";
echo "Now try the API again!\n";
