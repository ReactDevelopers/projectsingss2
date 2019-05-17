<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;

class UpdateCourseTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        $charset = Config::get('database.connections.mysql.charset');
        $collation = Config::get('database.connections.mysql.collation');
        DB::statement("ALTER TABLE `courses` CHANGE `placement_criteria` `placement_criteria` TEXT CHARACTER SET {$charset} COLLATE {$collation} NULL DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $charset = Config::get('database.connections.mysql.charset');
        $collation = Config::get('database.connections.mysql.collation');

        DB::statement("ALTER TABLE `courses` CHANGE `placement_criteria` `placement_criteria` VARCHAR(255) CHARACTER SET {$charset} COLLATE {$collation} NULL DEFAULT NULL;");
    }
}
