<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeranjangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keranjangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jenisBarang');
            $table->unsignedBigInteger('id_pesanan')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->float('ukuran');
            $table->integer('qty');
            $table->integer('biayaTambahan')->default(0);
            $table->enum('status_pengerjaan', ['menunggu', 'proses', 'selesai']);
            $table->integer('total');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_jenisBarang')->references('id')->on('jenis_barangs');
            $table->foreign('id_pesanan')->references('id')->on('pesanans');
            $table->foreign('id_user')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keranjangs');
    }
}
