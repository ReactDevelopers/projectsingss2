<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmNewCountriesCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__new_countries_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->tinyInteger('active')->nullable();

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
        Schema::dropIfExists('mm__new_countries_cities');
    }
}
