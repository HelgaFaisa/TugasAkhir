<?php
// database/migrations/2026_02_07_000001_create_pengajuan_kepulangan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengajuan_kepulangan', function (Blueprint $table) {
            $table->id();
            $table->string('id_pengajuan', 20)->unique(); // PGJ001, PGJ002, ...
            $table->string('id_santri', 20);
            $table->date('tanggal_pulang');
            $table->date('tanggal_kembali');
            $table->integer('durasi_izin'); // Auto-calculated
            $table->text('alasan');
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->text('catatan_review')->nullable(); // Catatan admin saat review
            $table->unsignedBigInteger('reviewed_by')->nullable(); // ID admin yang review
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('id_santri');
            $table->index('status');
            $table->index(['id_santri', 'status']);
            
            // Foreign keys
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_kepulangan');
    }
};