<?php
// database/migrations/xxxx_xx_xx_create_kepulangan_table.php

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
            $table->string('id_kepulangan', 20)->unique()->index();
            $table->string('id_santri', 20);
            $table->date('tanggal_izin');
            $table->date('tanggal_pulang');
            $table->date('tanggal_kembali');
            $table->integer('durasi_izin')->default(0)->comment('Durasi dalam hari');
            $table->text('alasan');
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak', 'Selesai'])
                  ->default('Menunggu');
            $table->string('approved_by', 100)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan')->nullable()->comment('Catatan approval atau penolakan');
            $table->timestamps();

            // Foreign key
            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Indexes untuk performa
            $table->index('status');
            $table->index('tanggal_pulang');
            $table->index('tanggal_kembali');
            $table->index(['id_santri', 'status']);
            $table->index(['tanggal_pulang', 'tanggal_kembali']);
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