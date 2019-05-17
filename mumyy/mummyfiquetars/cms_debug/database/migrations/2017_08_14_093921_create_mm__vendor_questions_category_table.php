<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorQuestionsCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__vendor_questions_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->integer('question_id')->unsigned()->nullable();
            $table->string('answer')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('vendor_id');
            $table->index('question_id');
            $table->foreign('vendor_id')
                  ->references('id')->on('users')
                  ->onDelete('CASCADE')
                  ->onUpdate('RESTRICT');
            $table->foreign('question_id')
                  ->references('id')->on('mm__questions')
                  ->onDelete('CASCADE')
                  ->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm__vendor_questions_category');
    }
}
