
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNAinctsToApproveFuturePlacementsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        $charset = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');

        DB::statement("ALTER TABLE `courses` CHANGE `cts_approve_future_placement` `cts_approve_future_placement` ENUM('Yes','No','NA') CHARACTER SET {$charset} COLLATE {$collation} NULL DEFAULT NULL;");



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

        DB::statement("ALTER TABLE `courses` CHANGE `cts_approve_future_placement` `cts_approve_future_placement` ENUM('Yes','No') CHARACTER SET {$charset} COLLATE {$collation} NULL DEFAULT NULL;");
    }
}
