<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSignatureRequestIdFieldToConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('confirmations', function (Blueprint $table) {
            $table->string('signature_request_id')->nullable()->default(null)->after('notes');
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
            $table->dropColumn('signature_request_id');
        });
    }
}
