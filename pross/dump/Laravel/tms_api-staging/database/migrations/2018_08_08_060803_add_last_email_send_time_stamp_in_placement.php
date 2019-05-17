<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastEmailSendTimeStampInPlacement extends Migration
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
            $table->dateTime('last_email_sent')->nullable();
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
            //
            $table->dropColumn('last_email_sent');
        });
    }
}
