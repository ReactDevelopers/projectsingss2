<?php

use Illuminate\Database\Seeder;

class DepartmentDataMigrate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::statement("UPDATE courses as c
JOIN departments as d ON d.id = c.department_id
SET c.department_id = (select id from departments where dept_code = 'NA')
where d.dept_code IN ('WRN','WRNL2','WRNL4','WRP','WRPL2','WRPL4','WSN','WSNL2','WSNL4','WSP','WSPL2','WSPL4','CW','CWL2','CWL4')");
        
    }
}
