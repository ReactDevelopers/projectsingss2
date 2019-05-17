<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDefaultValueOfProfileReportLeadsOfMmVendorsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE mm__vendors_settings MODIFY COLUMN profile_report_leads TINYINT(4) DEFAULT 1 COMMENT '0;only for new user; 1 = weekly; 2= monthly;'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE mm__vendors_settings MODIFY COLUMN profile_report_leads TINYINT(4) DEFAULT NULL COMMENT '0;only for new user; 1 = weekly; 2= monthly;'");
    }
}
