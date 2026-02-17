<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_pelanggarans', function (Blueprint $table) {
            // Status Kafaroh
            if (!Schema::hasColumn('riwayat_pelanggarans', 'is_kafaroh_selesai')) {
                $table->boolean('is_kafaroh_selesai')->default(false)->after('keterangan')->comment('Status kafaroh selesai/belum');
                $table->index('is_kafaroh_selesai');
            }
            if (!Schema::hasColumn('riwayat_pelanggarans', 'tanggal_kafaroh_selesai')) {
                $table->timestamp('tanggal_kafaroh_selesai')->nullable()->after('is_kafaroh_selesai')->comment('Tanggal kafaroh diselesaikan');
            }
            if (!Schema::hasColumn('riwayat_pelanggarans', 'admin_kafaroh_id')) {
                $table->unsignedBigInteger('admin_kafaroh_id')->nullable()->after('tanggal_kafaroh_selesai')->comment('Admin yang menyelesaikan kafaroh');
            }
            if (!Schema::hasColumn('riwayat_pelanggarans', 'catatan_kafaroh')) {
                $table->text('catatan_kafaroh')->nullable()->after('admin_kafaroh_id')->comment('Catatan saat kafaroh diselesaikan');
            }
            
            // Poin Asli (sebelum dilebur)
            if (!Schema::hasColumn('riwayat_pelanggarans', 'poin_asli')) {
                $table->integer('poin_asli')->after('poin')->nullable()->comment('Poin asli sebelum kafaroh');
            }
            
            // Status Publish ke Parent
            if (!Schema::hasColumn('riwayat_pelanggarans', 'is_published_to_parent')) {
                $table->boolean('is_published_to_parent')->default(false)->after('catatan_kafaroh')->comment('Apakah dikirim ke wali santri');
                $table->index('is_published_to_parent');
            }
            if (!Schema::hasColumn('riwayat_pelanggarans', 'tanggal_published')) {
                $table->timestamp('tanggal_published')->nullable()->after('is_published_to_parent')->comment('Tanggal dikirim ke wali');
            }
            if (!Schema::hasColumn('riwayat_pelanggarans', 'admin_published_id')) {
                $table->unsignedBigInteger('admin_published_id')->nullable()->after('tanggal_published')->comment('Admin yang publish ke wali');
            }
        });
        
        // Add foreign keys in separate statement with try-catch
        try {
            Schema::table('riwayat_pelanggarans', function (Blueprint $table) {
                if (Schema::hasColumn('riwayat_pelanggarans', 'admin_kafaroh_id')) {
                    $table->foreign('admin_kafaroh_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('set null');
                }
                      
                if (Schema::hasColumn('riwayat_pelanggarans', 'admin_published_id')) {
                    $table->foreign('admin_published_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('set null');
                }
            });
        } catch (\Exception $e) {
            // Foreign keys might already exist, ignore
        }
    }

    public function down(): void
    {
        Schema::table('riwayat_pelanggarans', function (Blueprint $table) {
            $table->dropForeign(['admin_kafaroh_id']);
            $table->dropForeign(['admin_published_id']);
            $table->dropIndex(['is_kafaroh_selesai']);
            $table->dropIndex(['is_published_to_parent']);
            $table->dropColumn([
                'is_kafaroh_selesai',
                'tanggal_kafaroh_selesai',
                'admin_kafaroh_id',
                'catatan_kafaroh',
                'poin_asli',
                'is_published_to_parent',
                'tanggal_published',
                'admin_published_id'
            ]);
        });
    }
};