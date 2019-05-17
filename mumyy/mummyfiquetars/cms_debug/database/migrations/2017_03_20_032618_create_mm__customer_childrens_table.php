<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmCustomerChildrensTable extends Migration
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
        --  Table structure for `mm__customer_childrens`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__customer_childrens`;
        CREATE TABLE `mm__customer_childrens` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) unsigned DEFAULT NULL,
          `name` varchar(255) DEFAULT NULL,
          `dob` timestamp NULL DEFAULT NULL,
          `sorts` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(255) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `user_id_2` (`user_id`),
          CONSTRAINT `mm__customer_childrens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__customer_childrens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->timestamp('dob')->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
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
        Schema::dropIfExists('mm__customer_childrens');
    }
}
