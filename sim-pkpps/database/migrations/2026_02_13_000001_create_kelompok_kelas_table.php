<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel kelompok_kelas untuk mengelompokkan kelas-kelas
     * (contoh: Pondok, Sekolah Formal, Umum)
     */
    public function up(): void
    {
        Schema::create('kelompok_kelas', function (Blueprint $table) {
            $table->id();
            
            // Kolom identitas kelompok
            $table->string('id_kelompok', 20)->unique()->comment('Kode unik kelompok: KEL001, KEL002, dst');
            $table->string('nama_kelompok', 100)->comment('Nama kelompok kelas');
            $table->text('deskripsi')->nullable()->comment('Deskripsi kelompok kelas');
            
            // Kolom untuk sorting dan status
            $table->unsignedTinyInteger('urutan')->default(0)->comment('Urutan tampilan kelompok');
            $table->boolean('is_active')->default(true)->comment('Status aktif kelompok');
            
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('id_kelompok');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_kelas');
    }
};
