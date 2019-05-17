<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgeColumnCustomerChildrensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__customer_childrens', function (Blueprint $table) {
            $table->text('age')->after('dob')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__customer_childrens', function (Blueprint $table) {
            $table->dropColumn('age');
        });
    }
}
