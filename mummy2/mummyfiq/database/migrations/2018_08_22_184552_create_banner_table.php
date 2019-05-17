<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTable extends Migration
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
        Schema::create('mm__banner', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->text('image')->nullable();
            $table->enum('type', ['0','1'])->nullable();
            $table->enum('status', ['0','1'])->nullable();
            $table->tinyInteger('is_deleted')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('mm__banner');
    }
}
