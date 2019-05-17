<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->integer('race_hotel_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->text('notes')->nullable();
            $table->timestamp('signed_on')->nullable()->default(null);
            $table->string('signed_file_uri')->nullable()->default(null);
            $table->date('expires_on')->nullable()->default(null);
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
        Schema::dropIfExists('confirmations');
    }
}
