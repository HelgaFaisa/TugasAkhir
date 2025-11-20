<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_id', 10)->unique(); // KT001, KT002...
            $table->string('nama_kategori', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('kategori_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_kegiatans');
    }
};