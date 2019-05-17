<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->integer('race_hotel_id')->unsigned()->nullable()->default(null);
            $table->integer('race_id')->unsigned()->nullable()->default(null);
            $table->integer('currency_id')->unsigned();
            $table->text('notes')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('deleted_by')->unsigned()->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_invoices');
    }
}
