<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel santri_kelas (pivot table) untuk relasi many-to-many
     * antara santri dan kelas. Santri bisa memiliki multiple kelas.
     */
    public function up(): void
    {
        Schema::create('santri_kelas', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->string('id_santri', 20)->comment('Relasi ke tabel santris');
            $table->unsignedBigInteger('id_kelas')->comment('Relasi ke tabel kelas');
            
            // Kolom tambahan
            $table->string('tahun_ajaran', 20)->comment('Tahun ajaran: 2024/2025');
            $table->boolean('is_primary')->default(false)->comment('Menandakan kelas utama santri');
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('cascade');
            
            $table->foreign('id_kelas')
                  ->references('id')
                  ->on('kelas')
                  ->onDelete('cascade');
            
            // Unique constraint: santri tidak bisa masuk kelas yang sama 2x di tahun yang sama
            $table->unique(['id_santri', 'id_kelas', 'tahun_ajaran'], 'santri_kelas_tahun_unique');
            
            // Index untuk performa query
            $table->index('id_santri');
            $table->index('id_kelas');
            $table->index('tahun_ajaran');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santri_kelas');
    }
};
