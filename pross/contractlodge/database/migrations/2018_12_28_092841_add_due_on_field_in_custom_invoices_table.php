<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDueOnFieldInCustomInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_invoices', function (Blueprint $table) {
            $table->date('due_on')->nullable()->default(null)->after('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('due_on')) {
                $table->dropColumn('due_on');
            }
        });
    }
}
