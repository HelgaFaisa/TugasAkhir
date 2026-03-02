<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to add Terlambat and Pulang
        DB::statement("ALTER TABLE absensi_kegiatans MODIFY COLUMN status ENUM('Hadir', 'Izin', 'Sakit', 'Alpa', 'Terlambat', 'Pulang') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE absensi_kegiatans MODIFY COLUMN status ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NOT NULL");
    }
};
