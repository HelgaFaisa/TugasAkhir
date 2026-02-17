<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PENTING: Jalankan migration ini SETELAH:
 * 1. php artisan migrate:santri-kelas-full --dry-run  (validasi)
 * 2. php artisan migrate:santri-kelas-full            (execute)
 * 3. Validasi data santri_kelas sudah benar
 * 4. Backup database
 * 
 * Command: php artisan migrate
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn('kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->enum('kelas', ['PB', 'Lambatan', 'Cepatan'])
                  ->nullable()
                  ->after('jenis_kelamin');
        });
    }
};
