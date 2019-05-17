<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckReadDeletedColumnToMmSendMessageAtble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__send_message', function (Blueprint $table) {
            $table->tinyInteger('is_vendor_read')->after('is_read')->default(1);
            $table->tinyInteger('is_customer_read')->after('is_read')->default(1);
            $table->timestamp('is_vendor_deleted')->after('is_deleted')->nullable();
            $table->timestamp('is_customer_deleted')->after('is_deleted')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__send_message', function (Blueprint $table) {
            $table->dropColumn(['is_vendor_read', 'is_customer_read', 'is_vendor_deleted', 'is_customer_deleted']); 
        });
    }
}
