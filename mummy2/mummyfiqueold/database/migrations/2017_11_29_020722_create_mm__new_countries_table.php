<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmNewCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__new_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sortname')->nullable();
            $table->string('name')->nullable();
            $table->integer('phonecode')->nullable();
            $table->tinyInteger('active')->nullable();
            $table->tinyInteger('sort')->nullable();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm__new_countries');
    }
}
