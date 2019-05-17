<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceFromToPiecesToMmVendorsProfilePricelistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_profile_pricelist', function (Blueprint $table) {
            $table->double('price_piece', 20, 2)->after('price_value')->nullable();
            $table->double('price_to', 20, 2)->after('price_value')->nullable();
            $table->double('price_from', 20, 2)->after('price_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_profile_pricelist', function (Blueprint $table) {
            $table->dropColumn(['price_piece', 'price_to', 'price_from']); 
        });
    }
}
