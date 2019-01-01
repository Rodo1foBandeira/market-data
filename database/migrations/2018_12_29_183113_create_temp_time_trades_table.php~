<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempTimeTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_time_trades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unix_timestamp');
            $table->string('ticker', 6);
            $table->integer('bank_id_purchase')->unsigned();
            $table->integer('bank_id_sale')->unsigned();
            $table->decimal('price',9,2)->nullable();
            $table->integer('volume')->nullable();
            $table->decimal('bid_price',9,2)->nullable();
            $table->integer('bid_qtd')->nullable();
            $table->decimal('ask_price',9,2)->nullable();
            $table->integer('ask_qtd')->nullable();
            
            $table->foreign('bank_id_purchase')->references('id')->on('banks');
            $table->foreign('bank_id_sale')->references('id')->on('banks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_time_trades');
    }
}
