<?php
// database/migrations/2025_10_24_01234_create_kategori_pelanggarans_table.php

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
        Schema::create('kategori_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->string('id_kategori', 10)->unique()->comment('ID Kategori format KP001, KP002, dst');
            $table->string('nama_pelanggaran', 255)->comment('Nama jenis pelanggaran');
            $table->integer('poin')->comment('Poin pelanggaran (1-100)');
            $table->timestamps();
            
            // Index untuk performa
            $table->index('id_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_pelanggarans');
    }
};