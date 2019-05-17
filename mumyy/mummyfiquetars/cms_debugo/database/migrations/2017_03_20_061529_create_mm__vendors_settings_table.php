<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsSettingsTable extends Migration
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
        --  Table structure for `mm__vendors_settings`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_settings`;
        CREATE TABLE `mm__vendors_settings` (
          `id` int(11) DEFAULT NULL,
          `vendor_id` int(11) DEFAULT NULL,
          `profile_report_leads` tinyint(1) DEFAULT NULL COMMENT '0;only for new user; 1 = weekly; 2= monthly; ',
          `someone_left_a_review` tinyint(1) DEFAULT '1' COMMENT '1 yes; 0 no',
          `addition_emails` text
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->tinyInteger('profile_report_leads')->comment('0;only for new user; 1 = weekly; 2= monthly; ')->nullable();
            $table->tinyInteger('someone_left_a_review')->comment('1 yes; 0 no')->default('1');
            $table->text('addition_emails')->nullable();

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
        Schema::dropIfExists('mm__vendors_settings');
    }
}
