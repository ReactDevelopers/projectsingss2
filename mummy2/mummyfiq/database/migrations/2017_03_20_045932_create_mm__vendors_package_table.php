<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsPackageTable extends Migration
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
        --  Table structure for `mm__vendors_package`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_package`;
        CREATE TABLE `mm__vendors_package` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned DEFAULT NULL,
          `package_id` int(11) DEFAULT NULL,
          `start_date` timestamp NULL DEFAULT NULL,
          `end_date` timestamp NULL DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `package_id` (`package_id`),
          CONSTRAINT `mm__vendors_package_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__vendors_package_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `mm__packages` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_package', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('package_id')->unsigned()->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->index('package_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('package_id')
                  ->references('id')->on('mm__packages')
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
        Schema::dropIfExists('mm__vendors_package');
    }
}
