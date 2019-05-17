<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfirmationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirmation_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('confirmation_id')->unsigned();
            $table->integer('races_hotels_inventory_id')->unsigned();
            $table->integer('quantity')->default(0);
            $table->date('check_in');
            $table->date('check_out');
            $table->decimal('rate', 14, 2);
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
        Schema::dropIfExists('confirmation_items');
    }
}
