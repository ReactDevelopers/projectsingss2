<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TriggerToCreateStatusHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('course_run_status_histories', function (Blueprint $table) {

            $table->dropColumn('created_at');
            
        });

        Schema::table('course_run_status_histories', function (Blueprint $table) {
            $table->dateTime('created_at');
        });

        Schema::table('placement_status_histories', function (Blueprint $table) {

            $table->dropColumn('created_at');
            
        });

        Schema::table('placement_status_histories', function (Blueprint $table) {
            $table->dateTime('created_at');
        });
        
        \DB::unprepared("DROP TRIGGER IF EXISTS `HISTORY_OF_CHANGE_PLACEMENT_STATUS`; CREATE TRIGGER `HISTORY_OF_CHANGE_PLACEMENT_STATUS` AFTER UPDATE ON `placements` FOR EACH ROW BEGIN IF ( NEW.current_status <> OLD.current_status ) THEN INSERT INTO `placement_status_histories` (placement_id, updater_id , current_status, created_at) values (NEW.id, NEW.updater_id, NEW.current_status, new.updated_at); END IF; END");
        \DB::unprepared("DROP TRIGGER IF EXISTS `HISTORY_OF_CHANGE_COURSE_RUN_STATUS`; CREATE TRIGGER `HISTORY_OF_CHANGE_COURSE_RUN_STATUS` AFTER UPDATE ON `course_runs` FOR EACH ROW BEGIN IF ( NEW.current_status <> OLD.current_status ) THEN INSERT INTO `course_run_status_histories` (course_run_id, updater_id , current_status, created_at) values (NEW.id, NEW.updater_id, NEW.current_status, new.updated_at); END IF; END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

        \DB::unprepared('DROP TRIGGER IF EXISTS `HISTORY_OF_CHANGE_PLACEMENT_STATUS`');
        \DB::unprepared('DROP TRIGGER IF EXISTS `HISTORY_OF_CHANGE_COURSE_RUN_STATUS`');

        Schema::table('course_run_status_histories', function (Blueprint $table) {

            $table->dropColumn('created_at');            
        });

        Schema::table('course_run_status_histories', function (Blueprint $table) {
            
            $table->timeTz('created_at');
        });

        Schema::table('placement_status_histories', function (Blueprint $table) {

            $table->dropColumn('created_at');            
        });

        Schema::table('placement_status_histories', function (Blueprint $table) {
            $table->timeTz('created_at');
        });

    }
}
