<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddDeletedByAndSoftDeleteFieldsInRacesHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('races_hotels', function (Blueprint $table) {
            $table->integer('deleted_by')->unsigned()->nullable()->default(null)->after('rooming_list_confirmed');
            $table->softDeletes()->after('deleted_by');
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
            $table->dropColumn('deleted_by');
            $table->dropColumn('deleted_at');
        });
    }
}
