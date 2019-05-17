<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationFieldToMmVendorsProfileTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_profile', function(Blueprint $table)
        {
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_profile', function(Blueprint $table)
        {
            $table->dropColumn(['lat', 'lng']);
        });
    }

}
