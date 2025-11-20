<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan_id', 10)->unique(); // KG001, KG002...
            $table->string('kategori_id', 10);
            $table->string('nama_kegiatan', 150);
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad']);
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('materi', 200)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('kegiatan_id');
            $table->index('kategori_id');
            $table->index('hari');
            
            $table->foreign('kategori_id')
                  ->references('kategori_id')
                  ->on('kategori_kegiatans')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};