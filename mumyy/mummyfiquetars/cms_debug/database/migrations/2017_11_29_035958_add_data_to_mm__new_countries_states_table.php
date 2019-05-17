<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToMmNewCountriesStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($file = fopen(__DIR__ ."/../../public/sql/mm__new_countries_states.sql", "r")) {
            while(!feof($file)) {
                $line = fgets($file);
                # do same stuff with the $line
                if(!empty($line)){
                    \DB::statement($line);
                }
            }
            fclose($file);
        }
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
