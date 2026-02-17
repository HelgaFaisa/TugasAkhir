<?php

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
        Schema::table('klasifikasi_pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('klasifikasi_pelanggarans', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->comment('Deskripsi klasifikasi');
            }
            
            if (!Schema::hasColumn('klasifikasi_pelanggarans', 'is_active')) {
                $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
                $table->index('is_active');
            }
            
            if (!Schema::hasColumn('klasifikasi_pelanggarans', 'urutan')) {
                $table->integer('urutan')->default(0)->comment('Urutan tampilan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klasifikasi_pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('klasifikasi_pelanggarans', 'is_active')) {
                $table->dropIndex(['is_active']);
                $table->dropColumn('is_active');
            }
            
            if (Schema::hasColumn('klasifikasi_pelanggarans', 'urutan')) {
                $table->dropColumn('urutan');
            }
            
            if (Schema::hasColumn('klasifikasi_pelanggarans', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
        });
    }
};
