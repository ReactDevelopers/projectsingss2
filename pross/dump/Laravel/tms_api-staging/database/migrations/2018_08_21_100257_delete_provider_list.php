<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteProviderList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('courses', function (Blueprint $table) {

            $table->dropForeign(['course_provider_id']);
            $table->dropColumn('course_provider_id');
            $table->string('course_provider')->nullable();
        });

        $provider = new CreateCourseProvidersTable();
        $provider->down();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

        $provider = new CreateCourseProvidersTable();
        $provider->up();

        Schema::table('courses', function (Blueprint $table) {

            $table->unsignedInteger('course_provider_id')->nullable();
            $table->foreign('course_provider_id')->references('id')->on('course_providers')->onDelete('SET NULL');
            $table->dropColumn('course_provider');
        });
        
    }
}
