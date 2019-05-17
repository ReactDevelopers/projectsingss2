<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmReviewerCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__reviewer_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('sort')->nullable();
            $table->nullableTimestamps();

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
        Schema::dropIfExists('mm__reviewer_category');
    }
}
