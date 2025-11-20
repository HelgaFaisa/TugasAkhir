<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_riwayat_pelanggarans_table.php

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
        Schema::create('riwayat_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->string('id_riwayat', 10)->unique()->comment('ID Riwayat format P001, P002, dst');
            $table->string('id_santri', 10)->comment('ID Santri dari tabel santris');
            $table->string('id_kategori', 10)->comment('ID Kategori dari tabel kategori_pelanggarans');
            $table->date('tanggal')->comment('Tanggal terjadinya pelanggaran');
            $table->integer('poin')->comment('Poin pelanggaran (diambil dari kategori)');
            $table->text('keterangan')->nullable()->comment('Keterangan tambahan tentang pelanggaran');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('id_kategori')
                  ->references('id_kategori')
                  ->on('kategori_pelanggarans')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Index untuk performa query
            $table->index('id_riwayat');
            $table->index('id_santri');
            $table->index('id_kategori');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pelanggarans');
    }
};