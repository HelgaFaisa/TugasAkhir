<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('uang_saku', function (Blueprint $table) {
            $table->id();
            $table->string('id_uang_saku', 10)->unique(); // SK001, SK002, dst
            $table->string('id_santri', 10);
            $table->enum('jenis_transaksi', ['pemasukan', 'pengeluaran']);
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_transaksi');
            $table->decimal('saldo_sebelum', 15, 2)->default(0);
            $table->decimal('saldo_sesudah', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->index(['id_santri', 'tanggal_transaksi']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('uang_saku');
    }
};