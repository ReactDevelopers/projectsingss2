<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_runs', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('course_code', 20);
            $table->foreign('course_code')->references('course_code')->on('courses')->onDelete('cascade');

            $table->enum('current_status',['Draft','Confirmed','Completed','Closed'])->default('Draft');

            $table->date('start_date');
            $table->date('end_date');
            $table->date('assessment_start_date')->nullable();
            $table->date('assessment_end_date')->nullable();
            $table->unsignedInteger('class_size');
            $table->enum('summary_uploaded',['Yes','No'])->default('No');
            $table->decimal('overall', 8,2)->nullable();
            $table->decimal('trainer_delivery', 8,2)->nullable();
            $table->decimal('content_relevance', 8,2)->nullable();
            $table->decimal('site_visits', 8,2)->nullable();
            $table->decimal('facilities', 8,2)->nullable();
            $table->decimal('admin', 8,2)->nullable();
            $table->decimal('response_rate', 8,2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_runs');
    }
}
