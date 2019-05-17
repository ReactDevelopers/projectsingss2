<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmReviewReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__user_review_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('review_id')->unsigned()->nullable();
            $table->integer('sender_id')->unsigned()->nullable()->comment('sender id');
            $table->integer('receiver_id')->unsigned()->nullable()->comment('receiver id');
            $table->text('reply_content')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->tinyInteger('is_deleted')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('review_id');
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->foreign('review_id')
                  ->references('id')->on('mm__user_reviews')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('sender_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('receiver_id')
                  ->references('id')->on('users')
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
        Schema::dropIfExists('mm__user_review_reply');
    }
}
