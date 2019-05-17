<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActiveColumnValueOfCountriesCitiesStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql_countries = "UPDATE `mm__countries` SET `active` = '1'";
        $sql_states = "UPDATE `mm__states` SET `active` = '1'";
        $sql_cities = "UPDATE `mm__cities` SET `active` = '1'";
        \DB::statement($sql_countries);
        \DB::statement($sql_states);
        \DB::statement($sql_cities);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql_countries1 = "UPDATE `mm__countries` SET `active` = '0'";
        $sql_countries2 = "UPDATE `mm__countries` SET `active` = '1' WHERE `name` = 'China' OR `name` = 'Hong Kong' OR `name` = 'Singapore' ";
        $sql_states1    = "UPDATE `mm__states` SET `active` = '0'";
        $sql_states2    = "UPDATE `mm__states` SET `active` = '1' WHERE `id` IN (763, 3186, 1636, 4044, 4045, 4046, 4047, 4048, 4049, 4050, 4051, 4052, 4053, 4054, 4055, 4056, 4057, 4058, 4059, 4060, 4061, 4062, 4063) ";
        $sql_cities1    = "UPDATE `mm__cities` SET `active` = '0'";
        $sql_cities2    = "UPDATE `mm__cities` SET `active` = '1' WHERE `id` IN (12304, 37541, 47364, 48315, 48316) ";
        \DB::statement($sql_countries1);
        \DB::statement($sql_countries2);
        \DB::statement($sql_states1);
        \DB::statement($sql_states2);
        \DB::statement($sql_cities1);
        \DB::statement($sql_cities2);

    }
}
