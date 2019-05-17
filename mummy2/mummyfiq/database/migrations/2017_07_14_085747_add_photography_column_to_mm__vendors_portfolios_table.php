<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotographyColumnToMmVendorsPortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_portfolios', function (Blueprint $table) {
            $table->string('photography')->after('tags')->nullable();
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
            $table->dropColumn(['photography']); 
        });
    }
}
