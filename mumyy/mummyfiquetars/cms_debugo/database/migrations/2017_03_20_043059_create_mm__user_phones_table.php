<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmUserPhonesTable extends Migration
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
        --  Table structure for `mm__user_phones`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__user_phones`;
        CREATE TABLE `mm__user_phones` (
          `id` int(11) DEFAULT NULL,
          `phone_number` varchar(255) DEFAULT NULL,
          `country_code` char(4) DEFAULT NULL,
          `is_primary` tinyint(1) DEFAULT NULL,
          `is_verifyed` tinyint(4) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          `user_id` int(10) unsigned DEFAULT NULL,
          KEY `user_id` (`user_id`),
          CONSTRAINT `mm__user_phones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__user_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number')->nullable();
            $table->char('country_code', 4)->nullable();
            $table->tinyInteger('is_primary')->nullable();
            $table->tinyInteger('is_verifyed')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->foreign('user_id')
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
        Schema::dropIfExists('mm__user_phones');
    }
}
