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
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->string('id_materi', 10)->unique()->comment('Format: M001, M002, dst');
            $table->string('kategori', 50)->index()->comment('Al-Quran, Hadist, Materi Tambahan');
            $table->enum('kelas', ['Lambatan', 'Cepatan', 'PB'])->index();
            $table->string('nama_kitab')->comment('Contoh: K. Sholah, Tafsir Jalalain');
            $table->integer('halaman_mulai')->unsigned();
            $table->integer('halaman_akhir')->unsigned();
            $table->integer('total_halaman')->unsigned()->comment('Auto-calculated');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Index untuk optimasi query
            $table->index(['kategori', 'kelas']);
            $table->index('nama_kitab');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};