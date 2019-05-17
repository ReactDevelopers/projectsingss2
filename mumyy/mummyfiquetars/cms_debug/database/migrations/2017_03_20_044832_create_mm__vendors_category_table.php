<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsCategoryTable extends Migration
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
        --  Table structure for `mm__vendors_category`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_category`;
        CREATE TABLE `mm__vendors_category` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned DEFAULT NULL,
          `category_id` int(11) DEFAULT NULL,
          `sub_category_id` int(11) DEFAULT NULL,
          `sub_category_custname` varchar(255) DEFAULT NULL,
          `is_primary` tinyint(4) DEFAULT NULL,
          `price_range_id` int(11) DEFAULT NULL,
          `sorts` int(5) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL COMMENT '0,1,2,3,4[active,pending,approved,disable]',
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `category_id` (`category_id`),
          KEY `user_id_2` (`user_id`),
          KEY `category_id_2` (`category_id`),
          KEY `user_id_3` (`user_id`),
          KEY `category_id_3` (`category_id`),
          KEY `user_id_4` (`user_id`),
          KEY `category_id_4` (`category_id`),
          KEY `user_id_5` (`user_id`),
          KEY `category_id_5` (`category_id`),
          KEY `price_range_id` (`price_range_id`),
          CONSTRAINT `mm__vendors_category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `mm__categories` (`id`),
          CONSTRAINT `mm__vendors_category_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__vendors_category_ibfk_3` FOREIGN KEY (`price_range_id`) REFERENCES `mm__price_range` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('sub_category_id')->unsigned()->nullable();
            $table->string('sub_category_custname')->nullable();            
            $table->tinyInteger('is_primary')->nullable();
            $table->integer('price_range_id')->unsigned()->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->comment('0,1,2,3,4[active,pending,approved,disable]')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('category_id');
            $table->index('user_id');
            $table->index('price_range_id');
            $table->foreign('category_id')
                  ->references('id')->on('mm__categories')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('price_range_id')
                  ->references('id')->on('mm__price_range')
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
        Schema::dropIfExists('mm__vendors_category');
    }
}
