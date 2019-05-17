<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmSettingsPricingTable extends Migration
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
        --  Table structure for `mm__settings_pricing`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__settings_pricing`;
        CREATE TABLE `mm__settings_pricing` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) DEFAULT NULL,
          `description` varchar(255) DEFAULT NULL,
          `price` decimal(10,0) DEFAULT NULL,
          `month` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__settings_pricing', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->decimal('price', 10,0)->nullable();
            $table->integer('month')->nullable();
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
        Schema::dropIfExists('mm__settings_pricing');
    }
}
