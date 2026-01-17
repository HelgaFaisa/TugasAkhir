<?php
// database/migrations/xxxx_xx_xx_create_kepulangan_reset_logs_table.php

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
        Schema::create('kepulangan_reset_logs', function (Blueprint $table) {
            $table->id();
            $table->string('id_santri', 20)->nullable()->comment('Null jika reset massal');
            $table->integer('total_hari_sebelum_reset')->default(0);
            $table->date('periode_mulai');
            $table->date('periode_akhir');
            $table->integer('kuota_tahunan');
            $table->enum('jenis_reset', ['individual', 'massal'])->default('individual');
            $table->string('reset_by', 100);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Foreign key (nullable untuk reset massal)
            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Indexes
            $table->index('id_santri');
            $table->index('jenis_reset');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepulangan_reset_logs');
    }
};