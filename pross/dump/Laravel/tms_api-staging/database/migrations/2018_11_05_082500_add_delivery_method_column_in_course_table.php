<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryMethodColumnInCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            //
            $table->unsignedInteger('delivery_method_id')->nullable();

            $table->foreign('delivery_method_id')
                  ->references('id')->on('delivery_methods')
                  ->onDelete('set null');
        });

        DB::statement('ALTER TABLE `courses` CHANGE `cost_per_pax` `cost_per_pax` DECIMAL(8,2) NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            //

            $table->dropForeign(['delivery_method_id']);
            $table->dropColumn('delivery_method_id');
        });
    }
}
