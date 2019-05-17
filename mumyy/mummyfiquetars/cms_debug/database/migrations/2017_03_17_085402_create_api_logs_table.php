<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiLogsTable extends Migration
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
        --  Table structure for `api_logs`
        -- ----------------------------
        DROP TABLE IF EXISTS `api_logs`;
        CREATE TABLE `api_logs` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `method` varchar(255) DEFAULT NULL,
          `request_url` varchar(255) DEFAULT NULL,
          `request_string` text,
          `response_string` text,
          `user_id` int(11) DEFAULT NULL,
          `request_ip` varchar(255) DEFAULT NULL,
          `device_type` varchar(255) DEFAULT NULL,
          `platform` enum('WEB-APP','MOBILE') DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
        */
        Schema::create('api_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('method')->nullable();
            $table->string('request_url')->nullable();
            $table->text('request_string')->nullable();
            $table->text('response_string')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('request_ip')->nullable();
            $table->string('device_type')->nullable();
            $table->string('platform')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_logs');
    }
}
