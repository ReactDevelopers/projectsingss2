<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('address', 50);
            $table->string('city', 50);
            $table->string('region', 50)->nullable()->default(null);
            $table->string('postal_code', 20);
            $table->integer('country_id')->unsigned();
            $table->string('phone', 20)->nullable()->default(null);
            $table->string('email', 50)->nullable()->default(null);
            $table->string('website', 100)->nullable()->default(null);
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
        Schema::dropIfExists('hotels');
    }
}
