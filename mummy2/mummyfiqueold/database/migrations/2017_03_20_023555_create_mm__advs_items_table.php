<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmAdvsItemsTable extends Migration
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
        --  Table structure for `mm__advs_items`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__advs_items`;
        CREATE TABLE `mm__advs_items` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `adv_id` int(11) DEFAULT NULL,
          `media` text,
          `media_thumb` text,
          `type` enum('IMAGE','VIDEO') DEFAULT NULL,
          `sorts` varchar(255) DEFAULT NULL,
          `total_click` varchar(255) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `adv_id` (`adv_id`),
          CONSTRAINT `mm__advs_items_ibfk_1` FOREIGN KEY (`adv_id`) REFERENCES `mm__advs_type` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__advs_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('adv_id')->unsigned()->nullable();
            $table->text('media')->nullable();
            $table->text('media_thumb')->nullable();
            $table->enum('type', ['IMAGE','VIDEO'])->nullable();
            $table->integer('sorts')->nullable();
            $table->integer('total_click')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('adv_id');
            $table->foreign('adv_id')
                  ->references('id')->on('mm__advs_type')
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
        Schema::dropIfExists('mm__advs_items');
    }
}
