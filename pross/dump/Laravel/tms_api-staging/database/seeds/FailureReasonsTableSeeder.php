<?php

use Illuminate\Database\Seeder;
use App\Models\FailureReason;

class FailureReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FailureReason::insert([
            ['failure_reason' => 'Disciplinary reason','created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['failure_reason' => 'Missed assessment','created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['failure_reason' => 'Missed deadline','created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['failure_reason' => 'Unfamiliar with subject','created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }
}
