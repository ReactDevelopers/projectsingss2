<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmNotificationsTable extends Migration
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
        --  Table structure for `mm__notifications`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__notifications`;
        CREATE TABLE `mm__notifications` (
          `id` int(11) DEFAULT NULL,
          `sender_id` int(11) unsigned DEFAULT NULL,
          `notification_type` enum('SMS','PUSH','EMAIL') DEFAULT NULL,
          `title` varchar(255) DEFAULT NULL,
          `content` text,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(255) DEFAULT NULL,
          KEY `sender_id` (`sender_id`),
          KEY `id` (`id`),
          CONSTRAINT `mm__notifications_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender_id')->unsigned()->nullable();
            $table->enum('notification_type', ['SMS','PUSH','EMAIL'])->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
            $table->nullableTimestamps();

            $table->engine = 'InnoDB';
            $table->index('sender_id');
            $table->index('id');
            $table->foreign('sender_id')
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
        Schema::dropIfExists('mm__notifications');
    }
}
