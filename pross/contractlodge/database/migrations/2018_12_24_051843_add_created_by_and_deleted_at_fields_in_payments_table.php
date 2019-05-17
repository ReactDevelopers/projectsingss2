<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByAndDeletedAtFieldsInPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('created_by')->unsigned()->nullable()->default(null)->after('paid_on');
            $table->integer('deleted_by')->unsigned()->nullable()->default(null)->after('created_by');
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
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('payments', 'deleted_by')) {
                $table->dropColumn('deleted_by');
            }
            if (Schema::hasColumn('payments', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
}
