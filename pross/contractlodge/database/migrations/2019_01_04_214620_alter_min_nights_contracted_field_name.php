<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMinNightsContractedFieldName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('races_hotels_inventory', function (Blueprint $table) {
            $table->renameColumn('min_nights_contracted', 'min_stays_contracted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('races_hotels_inventory', function (Blueprint $table) {
            $table->renameColumn('min_stays_contracted', 'min_nights_contracted');
        });
    }
}
