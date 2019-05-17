<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmUserReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        -- ----------------------------
        --  Table structure for `mm__user_reviews`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__user_reviews`;
        CREATE TABLE `mm__user_reviews` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned DEFAULT NULL,
          `vendor_id` int(11) unsigned DEFAULT NULL,
          `title` varchar(255) DEFAULT NULL,
          `content` varchar(255) DEFAULT NULL,
          `rating` int(1) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `vendor_id` (`vendor_id`),
          KEY `user_id_2` (`user_id`),
          KEY `vendor_id_2` (`vendor_id`),
          CONSTRAINT `mm__user_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__user_reviews_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__user_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->index('vendor_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('vendor_id')
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
        Schema::dropIfExists('mm__user_reviews');
    }
}
