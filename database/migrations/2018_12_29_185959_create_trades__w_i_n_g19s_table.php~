<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradesWING19sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades_wing19', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unix_timestamp_open')->nullable();
            $table->integer('unix_timestamp_close')->nullable();
            $table->decimal('open',9,2)->nullable();
            $table->decimal('minimum',9,2)->nullable();
            $table->decimal('maximum',9,2)->nullable();
            $table->decimal('close',9,2)->nullable();
            $table->integer('volume_purchase')->nullable();
            $table->integer('volume_sale')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades_wing19');
    }
}
