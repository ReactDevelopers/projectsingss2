<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemarkColumn extends Migration
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
            $table->string('remarks')->nullable();
            $table->enum('should_check_deconflict',['No','Yes'])->default('Yes');
        });

        Schema::table('placements', function (Blueprint $table) {
            $table->dropColumn('join_as');
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
            $table->dropColumn('remarks');
            $table->dropColumn('should_check_deconflict');
        });

        Schema::table('placements', function (Blueprint $table) {

            $table->enum('join_as',['Trainee','Trainer'])->default('Trainee'); 
        });
    }
}
