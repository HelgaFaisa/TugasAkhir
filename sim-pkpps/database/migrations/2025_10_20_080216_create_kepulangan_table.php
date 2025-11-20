<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_kepulangan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kepulangan', function (Blueprint $table) {
            $table->id();
            $table->string('id_kepulangan')->unique(); // KP001, KP002, dst
            $table->string('id_santri'); // Relasi ke santri (S001, S002, dst)
            $table->date('tanggal_izin'); // Tanggal pengajuan izin
            $table->date('tanggal_pulang'); // Tanggal mulai pulang
            $table->date('tanggal_kembali'); // Tanggal rencana kembali
            $table->integer('durasi_izin'); // Durasi dalam hari
            $table->text('alasan'); // Alasan kepulangan
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak', 'Selesai'])->default('Menunggu');
            $table->string('approved_by')->nullable(); // Admin yang menyetujui
            $table->timestamp('approved_at')->nullable(); // Waktu persetujuan
            $table->text('catatan')->nullable(); // Catatan dari admin
            $table->timestamps();

            // Foreign key
            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepulangan');
    }
};