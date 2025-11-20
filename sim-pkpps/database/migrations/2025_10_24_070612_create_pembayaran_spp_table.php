<?php
// database/migrations/xxxx_xx_xx_create_pembayaran_spp_table.php

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
        Schema::create('pembayaran_spp', function (Blueprint $table) {
            $table->id();
            $table->string('id_pembayaran')->unique(); // SPP001, SPP002, dst
            $table->string('id_santri'); // Foreign key ke tabel santris
            $table->integer('bulan'); // 1-12
            $table->integer('tahun'); // 2024, 2025, dst
            $table->decimal('nominal', 10, 2); // Nominal pembayaran
            $table->enum('status', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
            $table->date('tanggal_bayar')->nullable(); // Tanggal pembayaran
            $table->date('batas_bayar'); // Batas waktu pembayaran
            $table->text('keterangan')->nullable(); // Catatan tambahan
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi bulan & tahun per santri
            $table->unique(['id_santri', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_spp');
    }
};