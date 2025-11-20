<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('id_berita')->unique(); // B001, B002, dst
            $table->string('judul');
            $table->text('konten');
            $table->string('penulis');
            $table->string('gambar')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->enum('target_berita', ['semua', 'kelas_tertentu', 'santri_tertentu'])->default('semua');
            $table->json('target_kelas')->nullable(); // Untuk menyimpan array kelas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};