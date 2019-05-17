<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddDefaultsNullsForExhangeCurrencyIdAndCurrencyIdFieldInBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->integer('currency_id')->unsigned()->nullable()->default(null)->change();
        });
        Schema::table('bills', function (Blueprint $table) {
            $table->integer('exchange_currency_id')->unsigned()->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->integer('currency_id')->unsigned();
            $table->integer('exchange_currency_id')->unsigned();
        });
    }
}
