<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReviewerCategoryColumnToMmUserReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__user_reviews', function (Blueprint $table) {
            $table->string('reviewer_category')->after('vendor_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__user_reviews', function (Blueprint $table) {
            $table->dropColumn(['reviewer_category']); 
        });
    }
}
