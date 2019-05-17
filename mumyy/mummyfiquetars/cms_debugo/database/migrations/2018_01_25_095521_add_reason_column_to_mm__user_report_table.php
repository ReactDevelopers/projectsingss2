<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonColumnToMmUserReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__user_report', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__user_report', function (Blueprint $table) {
            $table->dropColumn(['reason']); 
        });
    }
}
