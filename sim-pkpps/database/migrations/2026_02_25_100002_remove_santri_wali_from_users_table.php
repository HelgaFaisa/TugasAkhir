<?php
// database/migrations/2026_02_25_100002_remove_santri_wali_from_users_table.php

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
        // -- Hapus semua user dengan role santri/wali --
        DB::table('users')->whereIn('role', ['santri', 'wali'])->delete();

        // -- Ubah enum role hanya untuk admin (raw SQL karena DBAL tidak support enum) --
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','akademik','pamong') NOT NULL DEFAULT 'akademik'");

        // -- Hapus kolom role_id jika ada --
        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','akademik','pamong','santri','wali') NOT NULL DEFAULT 'akademik'");

        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_id')->nullable()->after('role');
            });
        }
    }
};
