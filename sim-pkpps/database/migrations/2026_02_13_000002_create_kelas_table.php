<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel kelas untuk menyimpan detail kelas per kelompok
     * (contoh: PB, Lambatan, Cepatan, SD 1-6, SMP 7-9, SMA 10-12)
     */
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            
            // Kolom identitas kelas
            $table->string('kode_kelas', 20)->unique()->comment('Kode unik kelas: KLS001, KLS002, dst');
            $table->string('nama_kelas', 100)->comment('Nama kelas: PB, Lambatan, SD 1, dst');
            
            // Foreign key ke kelompok_kelas
            $table->string('id_kelompok', 20)->comment('Relasi ke kelompok_kelas');
            
            // Kolom untuk sorting dan status
            $table->unsignedTinyInteger('urutan')->default(0)->comment('Urutan tampilan dalam kelompok');
            $table->boolean('is_active')->default(true)->comment('Status aktif kelas');
            
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('id_kelompok')
                  ->references('id_kelompok')
                  ->on('kelompok_kelas')
                  ->onDelete('cascade');
            
            // Index untuk performa query
            $table->index('kode_kelas');
            $table->index('id_kelompok');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
