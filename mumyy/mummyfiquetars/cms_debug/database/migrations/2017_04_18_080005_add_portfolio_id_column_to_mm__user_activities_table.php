<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPortfolioIdColumnToMmUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__user_activities', function (Blueprint $table) {
            $table->integer('portfolio_id')->after('vendor_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__user_activities', function (Blueprint $table) {
            $table->dropColumn(['portfolio_id']);
        });
    }
}
