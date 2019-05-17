<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMultipleFieldsInPaymentableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paymentable', function (Blueprint $table) {
            $table->renameColumn('paymentable_type', 'payable_type');
        });
        Schema::table('paymentable', function (Blueprint $table) {
            $table->renameColumn('paymentable_id', 'payable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paymentable', function (Blueprint $table) {
            $table->renameColumn('payable_type', 'paymentable_type');
        });
        Schema::table('paymentable', function (Blueprint $table) {
            $table->renameColumn('payable_id', 'paymentable_id');
        });
    }
}
