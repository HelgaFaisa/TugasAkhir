<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pembinaan_sanksis')) {
            Schema::create('pembinaan_sanksis', function (Blueprint $table) {
                $table->id();
                $table->string('id_pembinaan', 10)->unique()->comment('ID Pembinaan format PS001, PS002, dst');
                $table->string('judul', 255)->comment('Judul pembinaan/sanksi');
                $table->text('konten')->comment('Konten pembinaan (HTML supported)');
                $table->integer('urutan')->default(0)->comment('Urutan tampilan');
                $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
                $table->timestamps();
                
                $table->index('id_pembinaan');
                $table->index('urutan');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembinaan_sanksis');
    }
};