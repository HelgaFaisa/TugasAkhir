<?php
// database/migrations/2026_02_24_000001_update_users_role_enum.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // -- Langkah 1: Perluas enum dengan semua nilai (lama + baru) --
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','super_admin','akademik','pamong','santri','wali') NOT NULL DEFAULT 'admin'");

        // -- Langkah 2: Update data lama 'admin' → 'super_admin' --
        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'super_admin']);

        // -- Langkah 3: Hapus 'admin' dari enum, set default baru --
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','akademik','pamong','santri','wali') NOT NULL DEFAULT 'super_admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // -- Langkah 1: Perluas enum kembali dengan 'admin' --
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','super_admin','akademik','pamong','santri','wali') NOT NULL DEFAULT 'super_admin'");

        // -- Langkah 2: Kembalikan role admin roles ke 'admin' --
        DB::table('users')
            ->whereIn('role', ['super_admin', 'akademik', 'pamong'])
            ->update(['role' => 'admin']);

        // -- Langkah 3: Kembalikan ke enum lama --
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','santri','wali') NOT NULL DEFAULT 'admin'");
    }
};
