<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacesHotelsInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races_hotels_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('race_hotel_id')->unsigned();
            $table->string('room_name');
            $table->decimal('min_night_hotel_rate', 14, 2)->nullable()->default(null);
            $table->decimal('min_night_client_rate', 14, 2)->nullable()->default(null);
            $table->integer('min_nights_contracted')->nullable()->default(null);
            $table->decimal('pre_post_night_hotel_rate', 14, 2)->nullable()->default(null);
            $table->decimal('pre_post_night_client_rate', 14, 2)->nullable()->default(null);
            $table->integer('pre_post_nights_contracted')->nullable()->default(null);
            $table->softDeletes();
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
        Schema::dropIfExists('races_hotels_inventory');
    }
}
