<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCategoryIdForeignKeyInMmSubcategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__sub_categories', function (Blueprint $table) {
            $table->dropForeign('mm__sub_categories_category_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__sub_categories', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')->on('mm__categories')
                  ->onDelete('RESTRICT')
                  ->onUpdate('RESTRICT');
        });
    }
}
