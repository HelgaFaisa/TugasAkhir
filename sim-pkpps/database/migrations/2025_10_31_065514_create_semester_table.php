<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->string('id_semester', 10)->unique()->comment('Format: SEM001, SEM002');
            $table->string('nama_semester')->comment('Contoh: Semester 1 2024/2025');
            $table->string('tahun_ajaran', 20)->comment('Contoh: 2024/2025')->index();
            $table->tinyInteger('periode')->comment('1 atau 2')->index();
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->boolean('is_active')->default(0)->comment('Hanya 1 semester yang aktif')->index();
            $table->timestamps();

            // Index untuk optimasi
            $table->index(['tahun_ajaran', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};