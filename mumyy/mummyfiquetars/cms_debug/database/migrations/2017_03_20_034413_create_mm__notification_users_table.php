<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmNotificationUsersTable extends Migration
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
        --  Table structure for `mm__notification_users`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__notification_users`;
        CREATE TABLE `mm__notification_users` (
          `id` int(11) NOT NULL,
          `sender_id` int(11) unsigned DEFAULT NULL,
          `receiver_id` int(11) unsigned DEFAULT NULL,
          `notification_id` int(11) DEFAULT NULL,
          `is_send` tinyint(4) DEFAULT NULL,
          `is_read` tinyint(4) DEFAULT NULL,
          `title` varchar(255) DEFAULT NULL,
          `content` text,
          `sorts` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `sender_id` (`sender_id`),
          KEY `receiver_id` (`receiver_id`),
          KEY `notification_id` (`notification_id`),
          CONSTRAINT `mm__notification_users_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__notification_users_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
          CONSTRAINT `mm__notification_users_ibfk_3` FOREIGN KEY (`notification_id`) REFERENCES `mm__notifications` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__notification_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender_id')->unsigned()->nullable();
            $table->integer('receiver_id')->unsigned()->nullable();
            $table->integer('notification_id')->unsigned()->nullable();
            $table->tinyInteger('is_send')->nullable();
            $table->tinyInteger('is_read')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('notification_id');
            $table->foreign('sender_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('receiver_id')
                  ->references('id')->on('users')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
            $table->foreign('notification_id')
                  ->references('id')->on('mm__notifications')
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
        Schema::dropIfExists('mm__notification_users');
    }
}
