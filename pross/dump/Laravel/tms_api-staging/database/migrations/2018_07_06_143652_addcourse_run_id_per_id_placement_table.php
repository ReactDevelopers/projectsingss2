<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddcourseRunIdPerIdPlacementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('placements', function (Blueprint $table) {
            
            $table->string('course_run_id_per_id',100)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->dropColumn('course_run_id_per_id');
        });
    }
}
