<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoThumbColumnToMmVendorsProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_profile', function (Blueprint $table) {
            $table->text('photo_thumb')->nullable()->after('photo_resize');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_profile', function (Blueprint $table) {
            $table->dropColumn(['photo_thumb']); 
        });
    }
}
