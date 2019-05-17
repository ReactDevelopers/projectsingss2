<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactPersonNameColumnToMmVendorsRequestsReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__vendors_requests_reviews', function (Blueprint $table) {
            $table->string('contact_person_name')->nullable()->after('email_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__vendors_requests_reviews', function (Blueprint $table) {
            $table->dropColumn(['contact_person_name']); 
        });
    }
}
