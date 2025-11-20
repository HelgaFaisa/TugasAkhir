<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_santris_table.php

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
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            
            // ID KUSTOM Santri
            $table->string('id_santri')->unique(); // Format S001, S002, ...
            
            // Data Utama
            $table->string('nis')->unique()->nullable(); // Nomor Induk Santri
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('kelas', ['PB', 'Lambatan', 'Cepatan']); // PB = Pembinaan
            $table->enum('status', ['Aktif', 'Lulus', 'Tidak Aktif'])->default('Aktif');
            $table->text('alamat_santri')->nullable();
            $table->string('daerah_asal')->nullable();
            
            // Data Wali Santri
            $table->string('nama_orang_tua')->nullable();
            $table->string('nomor_hp_ortu')->nullable();
            
            // Foreign key ke tabel walis (opsional, bisa ditambahkan nanti)
            // $table->foreignId('wali_id')->nullable()->constrained('walis')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};