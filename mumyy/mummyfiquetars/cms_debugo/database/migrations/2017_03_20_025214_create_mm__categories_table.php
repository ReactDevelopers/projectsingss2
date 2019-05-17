<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmCategoriesTable extends Migration
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
        --  Table structure for `mm__categories`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__categories`;
        CREATE TABLE `mm__categories` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) DEFAULT NULL,
          `description` text,
          `sorts` varchar(255) DEFAULT NULL,
          `country_id` int(11) NOT NULL,
          `status` tinyint(4) DEFAULT '0',
          `is_deleted` tinyint(4) DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `INDEX_countryid` (`country_id`),
          CONSTRAINT `FF_country_id01` FOREIGN KEY (`country_id`) REFERENCES `mm__countries` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('sorts')->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('country_id');
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
        Schema::dropIfExists('mm__categories');
    }
}
