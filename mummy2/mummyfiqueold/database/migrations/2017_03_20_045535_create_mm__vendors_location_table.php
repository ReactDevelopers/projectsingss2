<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsLocationTable extends Migration
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
        --  Table structure for `mm__vendors_location`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_location`;
        CREATE TABLE `mm__vendors_location` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) unsigned DEFAULT NULL,
          `country_id` int(11) DEFAULT NULL,
          `states_id` int(11) DEFAULT NULL,
          `city_id` int(11) DEFAULT NULL,
          `city_name` varchar(255) DEFAULT NULL,
          `is_primary` tinyint(1) DEFAULT NULL,
          `status` tinyint(1) DEFAULT NULL,
          `is_deleted` tinyint(1) DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `country_id` (`country_id`),
          CONSTRAINT `mm__vendors_location_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__vendors_location_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `mm__countries` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_location', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->integer('states_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->string('city_name')->nullable();           
            $table->tinyInteger('is_primary')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->index('country_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('country_id')
                  ->references('id')->on('mm__countries')
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
        Schema::dropIfExists('mm__vendors_location');
    }
}
