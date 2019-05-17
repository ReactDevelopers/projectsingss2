<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductNameColumnToMmVendorProfilePricelistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_profile_pricelist', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('user_id');
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
            $table->dropColumn(['product_name']); 
        });
    }
}
