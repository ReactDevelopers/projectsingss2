<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePriceNameColumnTypeOfMmVendorsProfilePricelistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('mm__vendors_profile_pricelist', function (Blueprint $table) {
        //     $table->enum('price_name', ['less than','between','greater than', 'start from', 'exactly'])->nullable()->change();
        // });
        $sql = "ALTER TABLE `mm__vendors_profile_pricelist` MODIFY `price_name` enum('less than','between','greater than', 'start from', 'exactly') DEFAULT NULL";
        \DB::statement($sql);
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
