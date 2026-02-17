<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('klasifikasi_pelanggarans')) {
            Schema::create('klasifikasi_pelanggarans', function (Blueprint $table) {
                $table->id();
                $table->string('id_klasifikasi', 10)->unique()->comment('ID Klasifikasi format KL001, KL002, dst');
                $table->string('nama_klasifikasi', 100)->comment('Nama klasifikasi: Ketertiban, Kerapian, Akhlaq, dll');
                $table->text('deskripsi')->nullable()->comment('Deskripsi klasifikasi');
                $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
                $table->integer('urutan')->default(0)->comment('Urutan tampilan');
                $table->timestamps();
                
                $table->index('id_klasifikasi');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('klasifikasi_pelanggarans');
    }
};