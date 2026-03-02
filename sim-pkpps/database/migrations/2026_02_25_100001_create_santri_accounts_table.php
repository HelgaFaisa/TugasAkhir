<?php
// database/migrations/2026_02_25_100001_create_santri_accounts_table.php

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
        Schema::create('santri_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('id_santri');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['santri', 'wali'])->default('santri');
            $table->rememberToken();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            $table->foreign('id_santri')
                  ->references('id_santri')
                  ->on('santris')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santri_accounts');
    }
};
