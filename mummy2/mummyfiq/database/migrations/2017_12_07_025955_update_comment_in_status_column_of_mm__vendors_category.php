<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCommentInStatusColumnOfMmVendorsCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `mm__vendors_category` MODIFY COLUMN `status` TINYINT(4) default NULL COMMENT '0: inactive, 1: active, 2, pending, 3: rejected'  ");
        // Schema::table('mm__vendors_category', function (Blueprint $table) {
        //     $table->tinyInteger('status')->nullable()->comment('0: inactive, 1: active, 2, pending, 3: reject')->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
