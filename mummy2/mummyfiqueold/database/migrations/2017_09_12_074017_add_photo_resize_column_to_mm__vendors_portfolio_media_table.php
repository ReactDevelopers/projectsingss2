<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoResizeColumnToMmVendorsPortfolioMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_portfolio_media', function (Blueprint $table) {
            $table->text('photo_resize')->nullable()->after('media_url_thumb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_portfolio_media', function (Blueprint $table) {
            $table->dropColumn(['photo_resize']); 
        });
    }
}
