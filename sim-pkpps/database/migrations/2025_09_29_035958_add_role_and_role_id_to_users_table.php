<?php
// database/migrations/YYYY_MM_DD_HHMMSS_add_role_and_role_id_to_users_table.php

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
        Schema::table('users', function (Blueprint $table) {
            // Role: admin, santri, wali
            $table->enum('role', ['admin', 'santri', 'wali'])->default('admin');
            // role_id: ID kustom dari tabel santris (S001) atau walis (WS001)
            $table->string('role_id')->nullable()->after('email');
            
            // Tambahkan kolom untuk menghubungkan ke tabel santris/walis
            // Akan diisi dengan id_santri atau id_wali
            // Note: Kita gunakan string role_id karena FK nya berbeda tabel dan formatnya string (S001, WS001)

            // Santri/Wali akan menggunakan 'username' sebagai pengganti email untuk login
            $table->string('username')->unique()->nullable()->after('name');
            
            // Mengubah email menjadi nullable karena tidak semua santri/wali punya email
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('role_id');
            $table->dropColumn('username');
            // Kembalikan email ke non-nullable jika diperlukan
            $table->string('email')->nullable(false)->change();
        });
    }
};