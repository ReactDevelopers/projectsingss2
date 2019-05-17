<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVendorPortfilio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* 
            Created by: Nhat Doan
        */
        Schema::table('mm__vendors_portfolios', function (Blueprint $table) {
            $table->integer('vendor_id')->unsigned()->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_portfolios', function (Blueprint $table) {
            $table->dropColumn('vendor_id');            
        });
    }
}
