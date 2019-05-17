<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('course_run_id');
            $table->foreign('course_run_id')->references('id')->on('course_runs')->onDelete('cascade');
            
            $table->unsignedInteger('personnel_number');
            $table->foreign('personnel_number')->references('personnel_number')->on('users')->onDelete('cascade');            

            $table->unique(['course_run_id','personnel_number']);

            $table->enum('result_uploaded',['Yes','No'])->default('No');
            $table->enum('attendance',['Present','Absent'])->nullable();
            $table->enum('assessment_results',['Pass','Fail'])->nullable();
            //absent_reason_id

            $table->unsignedInteger('absent_reason_id')->nullable();
            $table->foreign('absent_reason_id')->references('id')->on('absent_reasons')->onDelete('SET NULL');

            $table->unsignedInteger('failure_reason_id')->nullable();
            $table->foreign('failure_reason_id')->references('id')->on('failure_reasons')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('placements');
    }
}
