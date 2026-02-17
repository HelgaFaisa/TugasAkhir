<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelompokKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seed data master untuk kelompok_kelas (kategori kelas)
     */
    public function run(): void
    {
        echo "Seeding kelompok_kelas...\n";
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate table untuk clean state
        DB::table('kelompok_kelas')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $now = Carbon::now();
        
        $kelompokKelas = [
            [
                'id_kelompok' => 'KEL001',
                'nama_kelompok' => 'Kelas Pondok',
                'deskripsi' => 'Tingkatan kelas sistem pondok pesantren',
                'urutan' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_kelompok' => 'KEL002',
                'nama_kelompok' => 'Sekolah Formal',
                'deskripsi' => 'Kelas pendidikan formal (SD, SMP, SMA)',
                'urutan' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_kelompok' => 'KEL003',
                'nama_kelompok' => 'Umum',
                'deskripsi' => 'Untuk kegiatan yang diikuti semua santri',
                'urutan' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        
        // Bulk insert untuk efficiency
        DB::table('kelompok_kelas')->insert($kelompokKelas);
        
        echo "✓ Seeded " . count($kelompokKelas) . " kelompok kelas\n";
        echo "  - Kelas Pondok (KEL001)\n";
        echo "  - Sekolah Formal (KEL002)\n";
        echo "  - Umum (KEL003)\n";
    }
}
