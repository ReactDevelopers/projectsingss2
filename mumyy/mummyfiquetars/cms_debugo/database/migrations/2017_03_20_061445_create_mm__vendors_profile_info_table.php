<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsProfileInfoTable extends Migration
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
        --  Table structure for `mm__vendors_profile_info`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_profile_info`;
        CREATE TABLE `mm__vendors_profile_info` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned DEFAULT NULL,
          `question_id` int(11) DEFAULT NULL,
          `question` varchar(255) DEFAULT NULL,
          `awnser_text` text,
          `sorts` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `question_id` (`question_id`),
          CONSTRAINT `mm__vendors_profile_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__vendors_profile_info_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `mm__questions` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_profile_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('question_id')->unsigned()->nullable();
            $table->string('question')->nullable();
            $table->text('awnser_text')->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->index('question_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('question_id')
                  ->references('id')->on('mm__questions')
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
        Schema::dropIfExists('mm__vendors_profile_info');
    }
}
