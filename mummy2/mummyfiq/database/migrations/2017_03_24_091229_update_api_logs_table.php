<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_logs', function (Blueprint $table) {
            //
            $table->longText('request_string')->change();
            $table->longText('response_string')->change();

            //add
            $table->longText('request_header')->nullable();
            $table->longText('token')->nullable();
            $table->integer('duration');
            $table->text('agent_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_logs', function (Blueprint $table) {
            //
        });
    }
}
