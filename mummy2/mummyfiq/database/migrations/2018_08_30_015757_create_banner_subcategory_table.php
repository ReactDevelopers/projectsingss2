<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerSubcategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__banner_subcategory', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('banner_id')->unsigned()->nullable();
            $table->integer('subcategory')->unsigned()->nullable();
            // $table->string('category')->nullable();
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
        Schema::drop('mm__banner_subcategory');
    }
}
