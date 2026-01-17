<?php
// database/migrations/xxxx_xx_xx_create_kepulangan_settings_table.php

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
        Schema::create('kepulangan_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('kuota_maksimal')->default(12)->comment('Kuota hari izin per tahun');
            $table->date('periode_mulai')->comment('Awal periode kuota');
            $table->date('periode_akhir')->comment('Akhir periode kuota');
            $table->timestamp('terakhir_reset')->nullable();
            $table->string('reset_by', 100)->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('kepulangan_settings')->insert([
            'kuota_maksimal' => 12,
            'periode_mulai' => now()->startOfYear()->format('Y-m-d'),
            'periode_akhir' => now()->endOfYear()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepulangan_settings');
    }
};