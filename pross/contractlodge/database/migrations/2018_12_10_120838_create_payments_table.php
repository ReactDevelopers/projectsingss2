<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('currency_id')->unsigned();
            $table->string('payment_name');
            $table->decimal('amount_due', 14, 2)->nullable()->default(null);
            $table->date('due_on');
            $table->decimal('amount_paid', 14, 2)->nullable()->default(null);
            $table->date('paid_on')->nullable()->default(null);
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
        Schema::dropIfExists('payments');
    }
}
