<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmCountriesTable extends Migration
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
        --  Table structure for `mm__countries`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__countries`;
        CREATE TABLE `mm__countries` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `sortname` varchar(3) NOT NULL,
          `name` varchar(150) NOT NULL,
          `phonecode` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sortname')->nullable();
            $table->string('name')->nullable();
            $table->integer('phonecode')->nullable();

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
        Schema::dropIfExists('mm__countries');
    }
}
