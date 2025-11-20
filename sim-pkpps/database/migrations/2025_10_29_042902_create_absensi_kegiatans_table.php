<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('absensi_id', 10)->unique(); // A001, A002...
            $table->string('kegiatan_id', 10);
            $table->string('id_santri', 10);
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa']);
            $table->enum('metode_absen', ['Manual', 'RFID'])->default('Manual');
            $table->time('waktu_absen')->nullable();
            $table->timestamps();
            
            $table->index('absensi_id');
            $table->index('kegiatan_id');
            $table->index('id_santri');
            $table->index('tanggal');
            
            // Unique constraint: 1 santri hanya bisa absen 1x per kegiatan per hari
            $table->unique(['kegiatan_id', 'id_santri', 'tanggal']);
            
            $table->foreign('kegiatan_id')
                  ->references('kegiatan_id')
                  ->on('kegiatans')
                  ->onDelete('cascade');
                  
            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_kegiatans');
    }
};