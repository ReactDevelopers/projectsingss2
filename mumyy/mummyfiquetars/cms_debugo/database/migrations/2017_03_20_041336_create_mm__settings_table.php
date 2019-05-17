<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmSettingsTable extends Migration
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
        --  Table structure for `mm__settings`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__settings`;
        CREATE TABLE `mm__settings` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `key` varchar(255) DEFAULT NULL,
          `value` varchar(255) DEFAULT NULL,
          `sorts` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->nullable();

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
         Schema::dropIfExists('mm__settings');
    }
}
