<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNewColumnCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            //

            $table->enum('compulsory',['Yes','No'])->default('No');
            $table->enum('cts_approve_future_placement',['Yes','No'])->default('No');
            $table->string('placement_criteria')->nullable();
            $table->string('type_of_grant')->nullable();
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
            
            $table->dropColumn('compulsory');
            $table->dropColumn('cts_approve_future_placement');
            $table->dropColumn('placement_criteria');            
            $table->dropColumn('type_of_grant');            
        });
    }
}
