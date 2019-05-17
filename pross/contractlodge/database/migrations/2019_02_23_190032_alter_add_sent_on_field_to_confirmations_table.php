<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddSentOnFieldToConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('confirmations', function (Blueprint $table) {
            $table->datetime('sent_on')->nullable()->default(null)->after('expires_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('confirmations', function (Blueprint $table) {
            $table->dropColumn('sent_on');
        });
    }
}
