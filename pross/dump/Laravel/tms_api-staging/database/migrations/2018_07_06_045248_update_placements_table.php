<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('placements', function (Blueprint $table) {
            //
            $table->unsignedInteger('creator_id')->nullable()->comment('Who  created the record.');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedInteger('updater_id')->nullable()->comment('Whom last time updated the record.');
            $table->foreign('updater_id')->references('id')->on('users')->onDelete('set null');
            $table->enum('current_status',['Draft','Confirmed','Cancelled'])->default('Draft');
            $table->enum('is_email_send',['Yes','No'])->default('No');
            $table->enum('join_as',['Trainee','Trainer'])->default('Trainee'); 
            $table->softDeletes();
        });

        Schema::create('placement_status_histories', function (Blueprint $table) {
            
            $table->increments('id');

            $table->unsignedInteger('placement_id');
            $table->foreign('placement_id')->references('id')->on('placements')->onDelete('cascade');
            
            $table->unsignedInteger('updater_id')->nullable()->comment('Whom updated the status');
            $table->foreign('updater_id')->references('id')->on('users')->onDelete('set null');
            $table->enum('current_status',['Draft','Confirmed','Cancelled'])->default('Draft');
            $table->timeTz('created_at');	
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
            
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['updater_id']);
            $table->dropColumn('creator_id');
            $table->dropColumn('updater_id');

            $table->dropColumn('current_status');
            $table->dropColumn('is_email_send');
            $table->dropColumn('join_as');
            $table->dropColumn('deleted_at');
        });

        Schema::dropIfExists('placement_status_histories');
    }
}
