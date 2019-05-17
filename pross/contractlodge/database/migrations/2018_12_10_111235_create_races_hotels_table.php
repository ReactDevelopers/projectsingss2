<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacesHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races_hotels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('race_id')->unsigned();
            $table->integer('hotel_id')->unsigned();
            $table->integer('inventory_currency_id')->unsigned()->nullable()->default(null);
            $table->date('inventory_min_check_in')->nullable()->default(null);
            $table->date('inventory_min_check_out')->nullable()->default(null);
            $table->text('inventory_notes')->nullable();
            $table->date('rooming_list_sent')->nullable()->default(null);
            $table->date('rooming_list_confirmed')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('races_hotels');
    }
}
