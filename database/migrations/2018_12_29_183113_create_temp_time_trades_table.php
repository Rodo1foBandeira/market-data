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
            $table->integer('bank_code_purchase')->nullable();
            $table->integer('bank_code_sale')->nullable();
            $table->decimal('price',9,2)->nullable();
            $table->integer('qtd')->nullable();
            $table->integer('qtd_buss')->nullable();
            $table->integer('qtd_tot')->nullable();
            $table->decimal('bid_price',9,2)->nullable();
            $table->integer('bid_qtd')->nullable();
            $table->decimal('ask_price',9,2)->nullable();
            $table->integer('ask_qtd')->nullable();            
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
