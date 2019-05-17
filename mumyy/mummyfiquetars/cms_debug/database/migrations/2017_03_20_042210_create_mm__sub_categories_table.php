<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmSubCategoriesTable extends Migration
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
        --  Table structure for `mm__sub_categories`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__sub_categories`;
        CREATE TABLE `mm__sub_categories` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) DEFAULT NULL,
          `description` text,
          `sorts` varchar(255) DEFAULT NULL,
          `category_id` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT '0',
          `is_deleted` tinyint(4) DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `category_id` (`category_id`),
          CONSTRAINT `mm__sub_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `mm__categories` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('sorts')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
            
            $table->engine = 'InnoDB';
            $table->index('id');
            $table->index('category_id');
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
        Schema::dropIfExists('mm__sub_categories');
    }
}
