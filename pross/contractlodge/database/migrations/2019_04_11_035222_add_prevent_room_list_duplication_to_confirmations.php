<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreventRoomListDuplicationToConfirmations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('confirmations', function (Blueprint $table) {
            $table->boolean('prevent_room_list_duplication')->default(0)->after('sent_on');
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
            $table->dropColumn('prevent_room_list_duplication');
        });
    }
}
