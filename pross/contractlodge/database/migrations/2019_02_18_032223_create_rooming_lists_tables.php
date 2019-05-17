<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomingListsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooming_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('race_hotel_id');
            $table->unsignedInteger('client_id')->nullable()->default(null);
            $table->unsignedInteger('races_hotels_inventory_id');
            $table->timestamps();
        });

        Schema::create('rooming_list_guests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rooming_list_id');
            $table->unsignedInteger('race_hotel_id');
            $table->unsignedInteger('client_id')->nullable()->default(null);
            $table->unsignedInteger('races_hotels_inventory_id');
            $table->unsignedInteger('list_row_number');
            $table->unsignedInteger('client_row_number');
            $table->string('guest_name')->nullable()->default(null);
            $table->text('notes')->nullable()->default(null);
            $table->string('confirmation_number')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('rooming_list_guest_nights', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rooming_list_guest_id');
            $table->unsignedInteger('race_hotel_id');
            $table->date('date');
            $table->tinyInteger('status');
            $table->datetime('status_updated_at')->nullable()->default(null);
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
        Schema::dropIfExists('rooming_lists');
        Schema::dropIfExists('rooming_list_guests');
        Schema::dropIfExists('rooming_list_guest_nights');
    }
}
