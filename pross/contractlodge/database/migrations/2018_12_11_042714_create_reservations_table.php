<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('race_hotel_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('races_hotels_inventory_id')->unsigned();
            $table->string('guest_name', 200)->nullable()->default(null);
            $table->date('check_in')->nullable()->default(null);
            $table->date('check_out')->nullable()->default(null);
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
        Schema::dropIfExists('reservations');
    }
}
