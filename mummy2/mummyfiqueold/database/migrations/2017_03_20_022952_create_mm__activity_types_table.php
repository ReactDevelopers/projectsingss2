<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmActivityTypesTable extends Migration
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
        --  Table structure for `mm__activity_types`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__activity_types`;
        CREATE TABLE `mm__activity_types` (
          `type` enum('VIEWED_VENDOR_SITE','VIEWED_VENDOR_PRICELIST','VIEWED_VENDOR_FACEBOOK','VIEWED_VENDOR_INSTAGRAM','VIEWED_VENDOR_TWITTER','VIEWD_VENDOR_WEBSITE','SEND_INSTANT_MESSAGE','FAVOURITES','SAVED') NOT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */

        Schema::create('mm__activity_types', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['VIEWED_VENDOR_SITE','VIEWED_VENDOR_PRICELIST','VIEWED_VENDOR_FACEBOOK','VIEWED_VENDOR_INSTAGRAM','VIEWED_VENDOR_TWITTER','VIEWD_VENDOR_WEBSITE','SEND_INSTANT_MESSAGE','FAVOURITES','SAVED'])->nullable();
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
        Schema::dropIfExists('mm__activity_types');
    }
}
