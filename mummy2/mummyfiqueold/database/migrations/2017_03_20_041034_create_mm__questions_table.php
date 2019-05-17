<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmQuestionsTable extends Migration
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
        --  Table structure for `mm__questions`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__questions`;
        CREATE TABLE `mm__questions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `category_id` int(11) DEFAULT NULL,
          `question` varchar(255) DEFAULT NULL,
          `anwsers_type` text,
          `status` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `id` (`id`),
          KEY `category_id` (`category_id`),
          CONSTRAINT `mm__questions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `mm__categories` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm__questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('question')->nullable();
            $table->text('anwsers_type')->nullable();
            $table->tinyInteger('status')->nullable();
            
            $table->engine = 'InnoDB';
            $table->index('id');
            $table->index('category_id');
            $table->foreign('category_id')
                  ->references('id')->on('mm__categories')
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
        Schema::dropIfExists('mm__questions');
    }
}
