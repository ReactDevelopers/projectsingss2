<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddConfirmationContactIdAndInvoiceContactIdFieldsInContactablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contactables', function (Blueprint $table) {
            $table->integer('confirmation_contact_id')->unsigned()->nullable()->default(null)->after('contactable_id');
            $table->integer('invoice_contact_id')->unsigned()->nullable()->default(null)->after('confirmation_contact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contactables', function (Blueprint $table) {
            $table->dropColumn('confirmation_contact_id');
            $table->dropColumn('invoice_contact_id');
        });
    }
}
