<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            
            $table->unsignedInteger('creator_id')->nullable()->comment('Who  created the record.');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

            $table->unsignedInteger('updater_id')->nullable()->comment('Whom last time updated the record.');
            $table->foreign('updater_id')->references('id')->on('users')->onDelete('set null');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['updater_id']);
            $table->dropColumn('creator_id');
            $table->dropColumn('updater_id');
        });
    }
}
