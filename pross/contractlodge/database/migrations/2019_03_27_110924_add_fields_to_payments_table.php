<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->date('to_accounts_on')->nullable()->default(null)->after('paid_on');
            $table->string('invoice_number', 25)->nullable()->after('to_accounts_on');
            $table->date('invoice_date')->nullable()->default(null)->after('invoice_number');
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
            $table->dropColumn(['invoice_number', 'to_accounts_on', 'invoice_date']);
        });
    }
}
