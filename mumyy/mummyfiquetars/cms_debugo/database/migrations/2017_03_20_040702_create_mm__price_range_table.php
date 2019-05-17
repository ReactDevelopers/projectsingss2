<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmPriceRangeTable extends Migration
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
        --  Table structure for `mm__price_range`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__price_range`;
        CREATE TABLE `mm__price_range` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `price_name` varchar(255) DEFAULT NULL,
          `description` varchar(255) DEFAULT NULL,
          `sorts` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__price_range', function (Blueprint $table) {
            $table->increments('id');
            $table->string('price_name')->nullable();
            $table->string('description')->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm__price_range');
    }
}
