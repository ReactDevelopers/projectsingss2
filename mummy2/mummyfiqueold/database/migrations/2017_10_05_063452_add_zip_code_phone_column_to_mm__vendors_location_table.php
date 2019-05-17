<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZipCodePhoneColumnToMmVendorsLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_location', function (Blueprint $table) {
            $table->string('business_phone')->nullable()->after('city_name');
            $table->string('zip_code')->nullable()->after('city_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_location', function (Blueprint $table) {
            $table->dropColumn(['business_phone', 'zip_code']); 
        });
    }
}
