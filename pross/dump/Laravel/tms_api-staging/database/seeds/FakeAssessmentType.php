<?php

use Illuminate\Database\Seeder;
use App\Models\FailureReason;

class FakeAssessmentType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;
        while($i <= 10) {
            factory(\App\Models\AssessmentType::class)->create();
            $i++;
        }
    }
}
