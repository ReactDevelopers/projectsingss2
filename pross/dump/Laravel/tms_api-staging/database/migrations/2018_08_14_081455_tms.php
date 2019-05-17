<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (Schema::hasTable('updated_user_during_hrdu')) {
    		return;
    	}
        Schema::create('updated_user_during_hrdu', function (Blueprint $table) {
            //
            $table->integer('user_id')->unsigned()->primary();
            $table->foreign('user_id')
              ->references('id')->on('users')
              ->onDelete('cascade');

            $table->string('old_department_name')->nullable();
            $table->enum('is_activate',['Y','N'])->default('N'); 
        });

        \DB::unprepared("DROP TRIGGER IF EXISTS `track_user_who_moved_another_department_or_reactive`;CREATE  TRIGGER `track_user_who_moved_another_department_or_reactive` AFTER UPDATE ON `users` FOR EACH ROW BEGIN IF (NEW.department_id <> OLD.department_id OR (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) ) THEN INSERT INTO updated_user_during_hrdu (`user_id`,`old_department_name`,`is_activate`) VALUES (`OLD`.`id`, (SELECT dept_name from `departments` WHERE `id` = OLD.department_id) , IF(OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL,'Y','N') ) ON DUPLICATE KEY UPDATE `old_department_name` = VALUES(`old_department_name`), `is_activate` = VALUES(`is_activate`); END IF; END");

            
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('updated_user_during_hrdu');
        \DB::unprepared('DROP TRIGGER IF EXISTS `track_user_who_moved_another_department_or_reactive`');
    }
}
