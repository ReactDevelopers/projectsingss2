<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Customer\Entities\Country;

class AddSortColumnToMmCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__countries', function (Blueprint $table) {
            $table->tinyInteger('sort')->default(2);
        });

        // set sort of country singaporre is 1
        Country::where('name', 'Singapore')->update(['sort' => 1]);;

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__countries', function (Blueprint $table) {
            $table->dropColumn(['sort']); 
        });
    }
}
