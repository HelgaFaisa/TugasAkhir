<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_walis_table.php

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
        Schema::create('walis', function (Blueprint $table) {
            $table->id();
            // ID KUSTOM Wali Santri
            $table->string('id_wali')->unique(); // Format WS001, WS002, ...

            $table->string('nama_wali');
            $table->string('nomor_hp');
            $table->text('alamat')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walis');
    }
};