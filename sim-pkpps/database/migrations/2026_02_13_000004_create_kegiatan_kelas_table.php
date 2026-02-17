<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel kegiatan_kelas (pivot table) untuk relasi many-to-many
     * antara kegiatan dan kelas. Kegiatan bisa untuk multiple kelas.
     */
    public function up(): void
    {
        Schema::create('kegiatan_kelas', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->string('kegiatan_id', 20)->comment('Relasi ke tabel kegiatans');
            $table->unsignedBigInteger('id_kelas')->comment('Relasi ke tabel kelas');
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('kegiatan_id')
                  ->references('kegiatan_id')
                  ->on('kegiatans')
                  ->onDelete('cascade');
            
            $table->foreign('id_kelas')
                  ->references('id')
                  ->on('kelas')
                  ->onDelete('cascade');
            
            // Unique constraint: kegiatan tidak bisa assign ke kelas yang sama 2x
            $table->unique(['kegiatan_id', 'id_kelas'], 'kegiatan_kelas_unique');
            
            // Index untuk performa query
            $table->index('kegiatan_id');
            $table->index('id_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_kelas');
    }
};
