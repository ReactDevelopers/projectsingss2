<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortsColumnToMmNewCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__new_countries', function (Blueprint $table) {
            $table->string('sorts')->default(2)->after('sort');
        });
        \DB::statement("UPDATE mm__new_countries SET `sorts` = `sort` ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__new_countries', function (Blueprint $table) {
            $table->dropColumn(['sorts']); 
        });
    }
}
