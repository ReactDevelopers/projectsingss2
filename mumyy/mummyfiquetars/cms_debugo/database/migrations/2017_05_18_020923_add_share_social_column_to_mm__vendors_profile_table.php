<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShareSocialColumnToMmVendorsProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_profile', function (Blueprint $table) {
            $table->tinyInteger('follow_mummyfique_instagram')->after('dimension')->nullable();
            $table->tinyInteger('share_profile_on_pinterest')->after('dimension')->nullable();
            $table->tinyInteger('share_profile_on_twitter')->after('dimension')->nullable();
            $table->tinyInteger('share_profile_on_facebook')->after('dimension')->nullable();
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
            $table->dropColumn(['followe_mummyfique_instagram', 'share_profile_on_pinterest', 'share_profile_on_twitter', 'share_profile_on_facebook']); 
        });
    }
}
