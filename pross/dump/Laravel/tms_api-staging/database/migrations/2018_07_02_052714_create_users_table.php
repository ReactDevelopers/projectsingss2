<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->unsignedInteger('personnel_number')->unique();
            $table->unsignedInteger('supervisor_personnel_number')->nullable();  

            $table->string('designation')->nullable();
            $table->string('division')->nullable();
            $table->string('branch')->nullable();
            $table->string('section')->nullable();
            $table->unsignedInteger('num_success_login')->default(0);
            $table->dateTime('last_success_login_attempt');
            //num_success_login
            $table->timestamps();
            $table->softDeletes();

        });       


        Schema::create('roles', function (Blueprint $table) {

            $table->increments('id');            
            $table->string('name')->unique();
            $table->string('title');
            $table->string('description');
        });

        Schema::create('departments', function (Blueprint $table) {

            $table->increments('id');            
            $table->string('dept_code')->unique();
            $table->string('dept_name');
        });

        Schema::table('users', function (Blueprint $table) {

            $table->foreign('supervisor_personnel_number')
                ->references('personnel_number')->on('users')
                ->onDelete('SET NULL');

            $table->unsignedInteger('role_id')->after('supervisor_personnel_number')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('SET NULL');

            $table->unsignedInteger('department_id')->after('supervisor_personnel_number')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('SET NULL');
        });
        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('departments');
    }
}
