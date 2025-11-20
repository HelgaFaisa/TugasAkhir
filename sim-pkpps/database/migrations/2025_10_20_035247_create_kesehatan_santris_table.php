<?php

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
        Schema::create('kesehatan_santris', function (Blueprint $table) {
            $table->id();
            $table->string('id_kesehatan')->unique(); // K001, K002, dst
            $table->string('id_santri'); // Relasi ke tabel santris
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->text('keluhan');
            $table->text('catatan')->nullable();
            $table->enum('status', ['dirawat', 'sembuh', 'izin'])->default('dirawat');
            $table->timestamps();

            // Foreign key constraint
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
        Schema::dropIfExists('kesehatan_santris');
    }
};