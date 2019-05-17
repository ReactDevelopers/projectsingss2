<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {

            $table->increments('id');
            $table->string('course_code', 20)->unique();
            $table->string('title');
            $table->decimal('duration_in_days', 5,2);

            $table->unsignedInteger('programme_category_id')->nullable();
            $table->foreign('programme_category_id')->references('id')->on('programme_categories')->onDelete('SET NULL');

            $table->unsignedInteger('programme_type_id')->nullable();
            $table->foreign('programme_type_id')->references('id')->on('programme_types')->onDelete('SET NULL');

            $table->unsignedInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('SET NULL');

            $table->unsignedInteger('assessment_type_id')->nullable();
            $table->foreign('assessment_type_id')->references('id')->on('assessment_types')->onDelete('SET NULL');

            $table->enum('mandatory',['Yes','No','Yes by law'])->default('Yes');

            $table->unsignedInteger('training_location_id')->nullable();
            $table->foreign('training_location_id')->references('id')->on('training_locations')->onDelete('SET NULL');

            $table->string('delivery_method');

            $table->unsignedInteger('course_provider_id')->nullable();
            $table->foreign('course_provider_id')->references('id')->on('course_providers')->onDelete('SET NULL');

            $table->decimal('cost_per_pax', 8,2);
            $table->enum('subsidy',['Yes','No'])->default('No');
            $table->decimal('subsidy_value', 8,2);
            $table->string('vendor_email')->nullable();
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
        Schema::dropIfExists('courses');
    }
}
