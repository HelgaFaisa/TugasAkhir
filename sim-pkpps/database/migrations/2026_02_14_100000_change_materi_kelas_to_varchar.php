<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Mengubah kolom kelas di tabel materi dari ENUM('Lambatan','Cepatan','PB')
     * menjadi VARCHAR(100) agar bisa menerima nama kelas apapun dari tabel kelas.
     */
    public function up(): void
    {
        // Ubah ENUM ke VARCHAR menggunakan raw SQL (Laravel Schema tidak support ENUM->VARCHAR langsung)
        DB::statement("ALTER TABLE `materi` MODIFY COLUMN `kelas` VARCHAR(100) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke ENUM (hanya jika semua data masih valid)
        DB::statement("ALTER TABLE `materi` MODIFY COLUMN `kelas` ENUM('Lambatan','Cepatan','PB') NOT NULL");
    }
};
