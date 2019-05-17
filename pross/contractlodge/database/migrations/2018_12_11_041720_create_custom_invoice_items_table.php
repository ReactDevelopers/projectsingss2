<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_invoice_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('custom_invoice_id')->unsigned();
            $table->date('date')->nullable()->default(null);
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('rate', 14, 2)->default(0);
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
        Schema::dropIfExists('custom_invoice_items');
    }
}
