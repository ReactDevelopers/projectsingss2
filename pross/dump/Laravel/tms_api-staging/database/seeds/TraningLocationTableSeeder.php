<?php

use Illuminate\Database\Seeder;
use App\Models\TrainingLocation;

class TraningLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TrainingLocation::insert([

            ['location' => 'Local', 'created_at' => date('Y-m-d H:i:s') , 'updated_at' => date('Y-m-d H:i:s')],
            ['location' => 'Overseas', 'created_at' => date('Y-m-d H:i:s') , 'updated_at' => date('Y-m-d H:i:s')]
        ]);
    }
}
