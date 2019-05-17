<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsPortfolioMediaTable extends Migration
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
        --  Table structure for `mm__vendors_portfolio_media`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm__vendors_portfolio_media`;
        CREATE TABLE `mm__vendors_portfolio_media` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `portfolio_id` int(11) DEFAULT NULL,
          `media_url` text,
          `media_url_thumb` text,
          `media_type` enum('IMAGE','VIDEO') DEFAULT NULL,
          `media_source` enum('local','facebook','instagram') DEFAULT NULL,
          `sorts` int(11) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `portfolio_id` (`portfolio_id`),
          CONSTRAINT `mm__vendors_portfolio_media_ibfk_1` FOREIGN KEY (`portfolio_id`) REFERENCES `mm__vendors_portfolios` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        Schema::create('mm__vendors_portfolio_media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('portfolio_id')->unsigned()->nullable();
            $table->text('media_url')->nullable();
            $table->text('media_url_thumb')->nullable();
            $table->enum('media_type', ['IMAGE','VIDEO'])->nullable();
            $table->enum('media_source', ['local','facebook','instagram'])->nullable();
            $table->integer('sorts')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('is_deleted')->nullable();

            $table->engine = 'InnoDB';
            $table->index('portfolio_id');
            $table->foreign('portfolio_id')
                  ->references('id')->on('mm__vendors_portfolios')
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
        Schema::dropIfExists('mm__vendors_portfolio_media');
    }
}
