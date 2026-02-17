<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_pelanggarans', function (Blueprint $table) {
            // Tambah field klasifikasi
            if (!Schema::hasColumn('kategori_pelanggarans', 'id_klasifikasi')) {
                $table->string('id_klasifikasi', 10)->after('id_kategori')->nullable()->comment('ID Klasifikasi');
                $table->index('id_klasifikasi');
            }
            
            // Tambah kafaroh/taqorrub
            if (!Schema::hasColumn('kategori_pelanggarans', 'kafaroh')) {
                $table->text('kafaroh')->after('poin')->nullable()->comment('Kafaroh/Taqorrub yang harus dilakukan');
            }
            
            // Status aktif
            if (!Schema::hasColumn('kategori_pelanggarans', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('kafaroh')->comment('Status aktif/nonaktif');
                $table->index('is_active');
            }
        });
        
        // Add foreign key in a separate statement with try-catch
        try {
            Schema::table('kategori_pelanggarans', function (Blueprint $table) {
                if (Schema::hasColumn('kategori_pelanggarans', 'id_klasifikasi')) {
                    $table->foreign('id_klasifikasi')
                          ->references('id_klasifikasi')
                          ->on('klasifikasi_pelanggarans')
                          ->onDelete('set null')
                          ->onUpdate('cascade');
                }
            });
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
        }
    }

    public function down(): void
    {
        Schema::table('kategori_pelanggarans', function (Blueprint $table) {
            $table->dropForeign(['id_klasifikasi']);
            $table->dropIndex(['id_klasifikasi']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['id_klasifikasi', 'kafaroh', 'is_active']);
        });
    }
};