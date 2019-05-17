<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsColumnToMmUserReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__user_reviews', function (Blueprint $table) {
           $table->nullableTimestamps();            
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
           $table->dropColumn(['created_at', 'updated_at']); 
        });
    }
}
