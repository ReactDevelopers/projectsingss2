<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmStatesTable extends Migration
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
        --  Table structure for `mm__states`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__states`;
        CREATE TABLE `mm__states` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(30) NOT NULL,
          `country_id` int(11) NOT NULL DEFAULT '1',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=4121 DEFAULT CHARSET=latin1;
        */
        Schema::create('mm__states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('country_id')->default('1');

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
        Schema::dropIfExists('mm__states');
    }
}
