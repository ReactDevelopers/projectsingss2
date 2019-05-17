<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__banner_vendor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('banner_id')->unsigned()->nullable();
            $table->integer('vendor')->unsigned()->nullable();
            // $table->string('vendor')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm__banner_vendor');
    }
}
