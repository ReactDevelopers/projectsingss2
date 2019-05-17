<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlacementConflictView2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        DB::statement('DROP VIEW `my_and_subordinate_placement`;');
        DB::statement("CREATE VIEW `my_and_subordinate_placement` AS SELECT
                placements.id,
                placements.personnel_number,
                placements.personnel_number as placement_per_id,
                placements.current_status,
                'my' AS type,
                cr.id AS course_run_id,
                cr.course_code,
                cr.start_date,
                cr.end_date,
                cr.assessment_start_date,
                cr.assessment_end_date,
                cr.should_check_deconflict
            FROM
                `placements`
            JOIN course_runs AS cr
            ON
                cr.id = placements.course_run_id 
            WHERE
                placements.deleted_at IS NULL AND placements.current_status = 'Confirmed' and cr.deleted_at is NULL
            UNION ALL
            SELECT
                subordinate_p.id,
                u.supervisor_personnel_number AS personnel_number,
                subordinate_p.personnel_number as placement_per_id,
                subordinate_p.current_status,
                'subordinate' AS type,
                cr.id AS course_run_id,
                cr.course_code,
                cr.start_date,
                cr.end_date,
                cr.assessment_start_date,
                cr.assessment_end_date,
                cr.should_check_deconflict
            FROM
                placements AS subordinate_p
            JOIN course_runs AS cr
            ON
                cr.id = subordinate_p.course_run_id 
            JOIN users AS u
            ON
                u.personnel_number = subordinate_p.personnel_number AND u.supervisor_personnel_number IS NOT NULL AND u.deleted_at is NULL
            WHERE
                subordinate_p.deleted_at IS NULL AND subordinate_p.current_status = 'Confirmed' and cr.deleted_at is NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

        DB::statement('DROP VIEW `my_and_subordinate_placement`;');
    }
}
