<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmUserReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__user_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->comment('customer id');
            $table->integer('review_id')->unsigned()->nullable();
            $table->text('content')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->tinyInteger('is_deleted')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->index('review_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('review_id')
                  ->references('id')->on('mm__user_reviews')
                  ->onDelete('RESTRICT')
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
        Schema::dropIfExists('mm__user_report');
    }
}
