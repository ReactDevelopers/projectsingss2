<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAbsenteesColumnFromCourseRun extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_runs', function (Blueprint $table) {
            //

            $table->dropColumn('no_of_absentees');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_runs', function (Blueprint $table) {
            //

            $table->unsignedInteger('no_of_absentees')->nullable();
        });
    }
}
