<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsProfilePricelistTable extends Migration
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
        --  Table structure for `mm__vendors_profile_pricelist`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_profile_pricelist`;
        CREATE TABLE `mm__vendors_profile_pricelist` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) unsigned DEFAULT NULL,
          `category_id` int(11) DEFAULT NULL,
          `sub_category_name` varchar(255) DEFAULT NULL,
          `price_name` enum('less than','between','greater than') DEFAULT NULL,
          `price_value` text,
          `description` text,
          `sorts` int(11) DEFAULT NULL,
          `status` tinyint(1) DEFAULT NULL,
          `is_deleted` tinyint(1) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `category_id` (`category_id`),
          CONSTRAINT `mm__vendors_profile_pricelist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__vendors_profile_pricelist_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `mm__categories` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_profile_pricelist', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('sub_category_name')->nullable();
            $table->enum('price_name', ['less than','between','greater than'])->nullable();
            $table->text('price_value')->nullable();
            $table->text('description')->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->index('category_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('category_id')
                  ->references('id')->on('mm__categories')
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
        Schema::dropIfExists('mm__vendors_profile_pricelist');
    }
}
