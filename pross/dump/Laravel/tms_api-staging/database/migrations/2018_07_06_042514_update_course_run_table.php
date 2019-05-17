<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCourseRunTable extends Migration
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
            $table->unsignedInteger('creator_id')->nullable()->comment('Who  created the record.');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

            $table->unsignedInteger('updater_id')->nullable()->comment('Whom last time updated the record.');
            $table->foreign('updater_id')->references('id')->on('users')->onDelete('set null');
            
            $table->unsignedInteger('no_of_trainees')->nullable();
            $table->unsignedInteger('no_of_attendees')->nullable();
            $table->unsignedInteger('no_of_absentees')->nullable();
        });

        Schema::create('course_run_status_histories', function (Blueprint $table) {
            
            $table->increments('id');

            $table->unsignedInteger('course_run_id');
            $table->foreign('course_run_id')->references('id')->on('course_runs')->onDelete('cascade');
            
            $table->unsignedInteger('updater_id')->nullable()->comment('Whom updated the status');
            $table->foreign('updater_id')->references('id')->on('users')->onDelete('set null');
            $table->enum('current_status',['Draft','Confirmed','Completed','Closed'])->default('Draft');
            $table->timeTz('created_at');	
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
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['updater_id']);
            $table->dropColumn('creator_id');
            $table->dropColumn('updater_id');
            $table->dropColumn('no_of_trainees');
            $table->dropColumn('no_of_attendees');
            $table->dropColumn('no_of_absentees');
        });

        Schema::dropIfExists('course_run_status_histories');
    }
}
