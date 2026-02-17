<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seed data master untuk kelas (detail kelas per kelompok)
     */
    public function run(): void
    {
        echo "Seeding kelas...\n";
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate table untuk clean state
        DB::table('kelas')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $now = Carbon::now();
        
        $kelasList = [
            // Kelompok Pondok (KEL001)
            [
                'kode_kelas' => 'KLS001',
                'nama_kelas' => 'PB',
                'id_kelompok' => 'KEL001',
                'urutan' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS002',
                'nama_kelas' => 'Lambatan',
                'id_kelompok' => 'KEL001',
                'urutan' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS003',
                'nama_kelas' => 'Cepatan',
                'id_kelompok' => 'KEL001',
                'urutan' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Kelompok Sekolah Formal - SD (KEL002)
            [
                'kode_kelas' => 'KLS004',
                'nama_kelas' => 'SD 1',
                'id_kelompok' => 'KEL002',
                'urutan' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS005',
                'nama_kelas' => 'SD 2',
                'id_kelompok' => 'KEL002',
                'urutan' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS006',
                'nama_kelas' => 'SD 3',
                'id_kelompok' => 'KEL002',
                'urutan' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS007',
                'nama_kelas' => 'SD 4',
                'id_kelompok' => 'KEL002',
                'urutan' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS008',
                'nama_kelas' => 'SD 5',
                'id_kelompok' => 'KEL002',
                'urutan' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS009',
                'nama_kelas' => 'SD 6',
                'id_kelompok' => 'KEL002',
                'urutan' => 6,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Kelompok Sekolah Formal - SMP (KEL002)
            [
                'kode_kelas' => 'KLS010',
                'nama_kelas' => 'SMP 7',
                'id_kelompok' => 'KEL002',
                'urutan' => 7,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS011',
                'nama_kelas' => 'SMP 8',
                'id_kelompok' => 'KEL002',
                'urutan' => 8,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS012',
                'nama_kelas' => 'SMP 9',
                'id_kelompok' => 'KEL002',
                'urutan' => 9,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Kelompok Sekolah Formal - SMA (KEL002)
            [
                'kode_kelas' => 'KLS013',
                'nama_kelas' => 'SMA 10',
                'id_kelompok' => 'KEL002',
                'urutan' => 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS014',
                'nama_kelas' => 'SMA 11',
                'id_kelompok' => 'KEL002',
                'urutan' => 11,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_kelas' => 'KLS015',
                'nama_kelas' => 'SMA 12',
                'id_kelompok' => 'KEL002',
                'urutan' => 12,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        
        // Bulk insert untuk efficiency
        DB::table('kelas')->insert($kelasList);
        
        echo "✓ Seeded " . count($kelasList) . " kelas\n";
        echo "\n";
        echo "Kelas Pondok (3 kelas):\n";
        echo "  - PB (KLS001)\n";
        echo "  - Lambatan (KLS002)\n";
        echo "  - Cepatan (KLS003)\n";
        echo "\n";
        echo "Sekolah Formal (12 kelas):\n";
        echo "  - SD: 6 kelas (KLS004-KLS009)\n";
        echo "  - SMP: 3 kelas (KLS010-KLS012)\n";
        echo "  - SMA: 3 kelas (KLS013-KLS015)\n";
    }
}
