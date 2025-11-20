<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian', function (Blueprint $table) {
            $table->id();
            $table->string('id_capaian', 10)->unique()->comment('Format: CP001, CP002');
            $table->string('id_santri', 10)->comment('FK ke santri');
            $table->string('id_materi', 10)->comment('FK ke materi');
            $table->string('id_semester', 10)->comment('FK ke semester');
            $table->text('halaman_selesai')->comment('Format: 1-10,16-21,40,45-50');
            $table->decimal('persentase', 5, 2)->default(0)->comment('Auto-calculated');
            $table->text('catatan')->nullable();
            $table->date('tanggal_input');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('id_materi')->references('id_materi')->on('materi')->onDelete('cascade');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            // Index untuk optimasi query
            $table->index('id_santri');
            $table->index('id_materi');
            $table->index('id_semester');
            $table->index(['id_santri', 'id_semester']);
            $table->index(['id_materi', 'id_semester']);
            $table->index('persentase');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian');
    }
};