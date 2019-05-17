<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmCitiesTable extends Migration
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
        --  Table structure for `mm__cities`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__cities`;
        CREATE TABLE `mm__cities` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(30) NOT NULL,
          `state_id` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=48315 DEFAULT CHARSET=latin1;
        */
        Schema::create('mm__cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->nullable();
            $table->integer('state_id')->nullable();

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
        Schema::dropIfExists('mm__cities');
    }
}
