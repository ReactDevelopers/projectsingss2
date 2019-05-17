<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewColumnToMmVendorsPortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_portfolios', function (Blueprint $table) {
            $table->integer('views')->after('tags')->default(0);
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
            $table->dropColumn(['views']); 
        });
    }
}
