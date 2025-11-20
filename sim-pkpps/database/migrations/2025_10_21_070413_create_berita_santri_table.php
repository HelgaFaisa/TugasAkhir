<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita_santri', function (Blueprint $table) {
            $table->id();
            $table->string('id_berita');
            $table->string('id_santri');
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamp('tanggal_baca')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_berita')->references('id_berita')->on('berita')->onDelete('cascade');
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['id_berita', 'id_santri']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_santri');
    }
};