<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pelanggan');
            $table->integer('sub_total');
            $table->integer('diskon')->default('0');
            $table->integer('total_biaya');
            $table->enum('status_proses', ['menunggu', 'proses', 'selesai'])->default('menunggu');
            $table->enum('status_bayar', ['belum', 'lunas'])->default('belum');
            $table->date('tanggal_pesan');
            $table->date('deadline');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id')->on('pelanggans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pesanans');
    }
}
