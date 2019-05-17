<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddRoomingListNotesFieldInRacesHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('races_hotels', function (Blueprint $table) {
            $table->string('rooming_list_notes', 255)->nullable()->after('rooming_list_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('races_hotels', function (Blueprint $table) {
            $table->dropColumn('rooming_list_notes');
        });
    }
}
