<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendMailWeeklyMonthlyColumnToMmVendorsProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_profile', function (Blueprint $table) {
            $table->tinyInteger('send_mail_monthly')->default(1);
            $table->tinyInteger('send_mail_weekly')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_profile', function (Blueprint $table) {
            $table->dropColumn(['send_mail_monthly', 'send_mail_weekly']); 
        });
    }
}
