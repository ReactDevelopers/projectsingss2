<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleDesctiptionColumnToMmAdvsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__advs_items', function (Blueprint $table) {
            $table->text('description')->after('adv_id')->nullable();
            $table->string('title')->after('adv_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__advs_items', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }
}
