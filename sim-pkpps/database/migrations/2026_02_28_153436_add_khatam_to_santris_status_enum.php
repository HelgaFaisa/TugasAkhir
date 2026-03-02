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
        // Already applied manually — ensures 'Khatam' is a valid status value
        DB::statement("ALTER TABLE santris MODIFY COLUMN status ENUM('Aktif','Lulus','Tidak Aktif','Khatam') NOT NULL DEFAULT 'Aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE santris MODIFY COLUMN status ENUM('Aktif','Lulus','Tidak Aktif') NOT NULL DEFAULT 'Aktif'");
    }
};
