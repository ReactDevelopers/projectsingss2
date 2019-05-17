<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReminderAtToMmVendorsCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_credit', function (Blueprint $table) {
            $table->timestamp('reminder_at')->nullable()->after('point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_credit', function (Blueprint $table) {
            $table->dropColumn(['reminder_at']); 
        });
    }
}
