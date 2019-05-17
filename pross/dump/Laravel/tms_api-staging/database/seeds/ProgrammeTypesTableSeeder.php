<?php

use Illuminate\Database\Seeder;
use App\Models\ProgrammeType;

class ProgrammeTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProgrammeType::insert([

            [
                'prog_type_code' => 'ACT',
                'prog_type_name' =>'ACT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'CON',
                'prog_type_name' =>'CON',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'CSV',
                'prog_type_name' =>'CSV',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'CYS',
                'prog_type_name' =>'CYS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'DGI',
                'prog_type_name' =>'DGI',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'ENF',
                'prog_type_name' =>'ENF',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'EXD',
                'prog_type_name' =>'EXD',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'FNP',
                'prog_type_name' =>'FNP',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'HRM',
                'prog_type_name' =>'HRM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'IA',
                'prog_type_name' =>'IA',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'INT',
                'prog_type_name' =>'INT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'LDS',
                'prog_type_name' =>'LDS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'LST',
                'prog_type_name' =>'LST',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'OEX',
                'prog_type_name' =>'OEX',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'SOJT',
                'prog_type_name' =>'SOJT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'ONB',
                'prog_type_name' =>'ONB',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'OVS',
                'prog_type_name' =>'OVS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'prog_type_code' => 'Work Skills Qualification',
                'prog_type_name' =>'Work Skills Qualification',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'SAF',
                'prog_type_name' =>'SAF',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'SOF',
                'prog_type_name' =>'SOF',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'TEC',
                'prog_type_name' =>'TEC',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'WSQ',
                'prog_type_name' =>'WSQ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_type_code' => 'OTH',
                'prog_type_name' =>'OTH',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            ['prog_type_code'=> 'ACT', 'prog_type_name'=>'ACT','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'CON', 'prog_type_name'=>'CON','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'CSV', 'prog_type_name'=>'CSV','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'CYS', 'prog_type_name'=>'CYS','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'DGL', 'prog_type_name'=>'DGL','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'ENF', 'prog_type_name'=>'ENF','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'EXD', 'prog_type_name'=>'EXD','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'TEC', 'prog_type_name'=>'TEC','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'FNP', 'prog_type_name'=>'FNP','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'HRM', 'prog_type_name'=>'HRM','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'IAU', 'prog_type_name'=>'IAU','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'INT', 'prog_type_name'=>'INT','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'LDS', 'prog_type_name'=>'LDS','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'LST', 'prog_type_name'=>'LST','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'ONB', 'prog_type_name'=>'ONB','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'OEX', 'prog_type_name'=>'OEX','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'SAF', 'prog_type_name'=>'SAF','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'SOF', 'prog_type_name'=>'SOF','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'OJT', 'prog_type_name'=>'OJT','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'WSQ', 'prog_type_name'=>'WSQ','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'OTH', 'prog_type_name'=>'OTH','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')]
        ]);
    }
}
