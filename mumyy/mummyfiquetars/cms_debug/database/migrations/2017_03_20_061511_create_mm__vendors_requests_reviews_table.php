<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsRequestsReviewsTable extends Migration
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
        --  Table structure for `mm__vendors_requests_reviews`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_requests_reviews`;
        CREATE TABLE `mm__vendors_requests_reviews` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `sent_to_customers` text,
          `vendor_id` int(255) unsigned NOT NULL,
          `message` varchar(255) NOT NULL,
          `email_content` text,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(255) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `vendor_id` (`vendor_id`),
          KEY `vendor_id_2` (`vendor_id`),
          CONSTRAINT `mm__vendors_requests_reviews_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_requests_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->text('sent_to_customers')->nullable();
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->string('message')->nullable();
            $table->text('email_content')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('vendor_id');
            $table->foreign('vendor_id')
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
        Schema::dropIfExists('mm__vendors_requests_reviews');
    }
}
