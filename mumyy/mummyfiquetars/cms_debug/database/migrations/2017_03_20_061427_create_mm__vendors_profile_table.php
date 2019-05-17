<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsProfileTable extends Migration
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
        --  Table structure for `mm__vendors_profile`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_profile`;
        CREATE TABLE `mm__vendors_profile` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned NOT NULL,
          `business_name` varchar(255) DEFAULT NULL COMMENT 'business name of vendor',
          `business_phone` varchar(15) DEFAULT NULL,
          `business_phone2` varchar(255) DEFAULT NULL,
          `business_phone3` varchar(255) DEFAULT NULL,
          `business_address` varchar(255) DEFAULT NULL,
          `zip_code` int(8) DEFAULT NULL,
          `website` varchar(255) DEFAULT NULL,
          `how_know_mummy` text COMMENT 'json format data {"name of question": "children data"}',
          `created_by` int(11) DEFAULT NULL,
          `photo` text COMMENT 'PNG, JPEG 250x250',
          `about` text,
          `contact_email` varchar(255) DEFAULT NULL,
          `social_media_link` varchar(255) DEFAULT NULL,
          `instagram_id` varchar(255) DEFAULT NULL,
          `instagram_showfeed` tinyint(4) DEFAULT '0' COMMENT '0: no; 1: yes',
          `others_social_data` text COMMENT 'json format {"whatsapp": "@whatsapp_id"}',
          `information` text,
          `rating_points` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `user_id_2` (`user_id`),
          CONSTRAINT `mm__vendors_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__vendors_profile', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('business_name')->comment('business name of vendor')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_phone2')->nullable();
            $table->string('business_phone3')->nullable();
            $table->string('business_address')->nullable();
            $table->integer('zip_code')->nullable();
            $table->string('website')->nullable();
            $table->text('how_know_mummy')->comment("json format data {'name of question': 'children data'}")->nullable();
            $table->integer('created_by')->nullable();
            $table->text('photo')->comment('PNG, JPEG 250x250')->nullable();
            $table->text('about')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('social_media_link')->nullable();
            $table->string('instagram_id')->nullable();
            $table->tinyInteger('instagram_showfeed')->comment('0: no; 1: yes')->default(0);
            $table->text('others_social_data')->comment("json format {'whatsapp': '@whatsapp_id'}")->nullable();
            $table->text('information')->nullable();
            $table->integer('rating_points')->nullable();
            
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
        Schema::dropIfExists('mm__vendors_profile');
    }
}
