<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmNewCountriesStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__new_countries_states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('country_id')->default('1');
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
        Schema::dropIfExists('mm__new_countries_states');
    }
}
