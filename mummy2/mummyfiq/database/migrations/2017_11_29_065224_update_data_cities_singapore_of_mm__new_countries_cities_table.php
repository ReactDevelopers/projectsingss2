<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDataCitiesSingaporeOfMmNewCountriesCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("UPDATE mm__new_countries_cities SET `active` = '1'");
        \DB::statement("UPDATE mm__new_countries_cities SET `active` = '0' WHERE `name` != 'Singapore' AND `country_id` = '194' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
