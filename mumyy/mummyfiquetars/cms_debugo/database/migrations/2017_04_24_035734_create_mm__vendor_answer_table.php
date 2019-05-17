<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__vendors_answer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->string('question_key')->nullable();
            $table->string('question')->nullable();
            $table->text('answer')->nullable();
            $table->string('extra')->nullable();
            $table->integer('sort')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('vendor_id');
            $table->foreign('vendor_id')
                  ->references('id')->on('users')
                  ->onDelete('CASCADE')
                  ->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm__vendors_answer');
    }
}
