<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatusColumnOfMmUserReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('mm__user_reviews', function (Blueprint $table) {
        //     $table->tinyInteger('status')->default(1)->change();
        // });
        \DB::statement("ALTER TABLE mm__user_reviews MODIFY COLUMN status TINYINT(4) DEFAULT 1");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE mm__user_reviews MODIFY COLUMN status TINYINT(4) DEFAULT NULL");
    }
}
