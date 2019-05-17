<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmPackagesTable extends Migration
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
        --  Table structure for `mm__packages`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__packages`;
        CREATE TABLE `mm__packages` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) DEFAULT NULL,
          `price` decimal(10,0) DEFAULT NULL,
          `type` enum('FREE','SLIVER','GOLD') DEFAULT NULL,
          `services` text,
          `country_id` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `country_id` (`country_id`),
          CONSTRAINT `mm__packages_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `mm__countries` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->decimal('price', 10, 0)->nullable();
            $table->enum('type', ['FREE','SLIVER','GOLD'])->nullable();
            $table->text('services')->nullable();
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
        Schema::dropIfExists('mm__packages');
    }
}
