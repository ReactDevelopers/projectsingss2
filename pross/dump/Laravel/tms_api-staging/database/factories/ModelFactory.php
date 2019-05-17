<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'personnel_number' => $faker->unique()->numberBetween(21147,50000000),
        'role_id' => $faker->randomElement([1,2])
    ];
});


$factory->define(App\Models\AssessmentType::class, function (Faker\Generator $faker) {
    return [
        'assessment_type_name' => $faker->unique()->word
    ];
});

$factory->define(App\Models\Course::class, function (Faker\Generator $faker) {
    
    $grantsubsidy_yn = $faker->randomElement(['Yes','No']);
    $if_yes_provide_value = 0;
    $cost_per_pax = $faker->randomFloat(2, 100,999999);
    if($grantsubsidy_yn === 'Yes') {
        
        $min = $cost_per_pax - 50;
        $max = $cost_per_pax;
        $if_yes_provide_value = $faker->randomFloat(2, $min, $max);
    }

    return [
        'course_code' => $faker->unique()->regexify('[A-Z]{4}[0-9]{2,8}'),
        'title' => $faker->text(255),
        'duration_in_days'=>$faker->numberBetween(2,30),
        'programme_category_id'=> (App\Models\ProgrammeCategory::inRandomOrder()->first())->id,
        'programme_type_id' => (App\Models\ProgrammeType::inRandomOrder()->first())->id,
        'mandatory' => $faker->randomElement(['Yes','Yes by law','No']),
        //'delivery_method' => $faker->text(255),
        'training_location_id'=>(App\Models\TrainingLocation::inRandomOrder()->first())->id,
        'course_provider' => $faker->text(30),
        'cost_per_pax' => $cost_per_pax,
        'subsidy'=>$grantsubsidy_yn,
        'subsidy_value' => $if_yes_provide_value,
        'vendor_email' => $faker->text(255)        
    ];
});


$factory->define(App\Models\CourseRun::class, function (Faker\Generator $faker, $custom_data ) {

    //$course = factory(App\Models\Course::class)->make();
    $max_date = \Carbon\Carbon::now()->addYears(1);
    
    $start_date = isset($custom_data['start_date']) ? $custom_data['start_date'] : $faker->date('Y-m-d', $max_date);
    $end_date = isset($custom_data['end_date']) ? $custom_data['end_date'] : \Carbon\Carbon::parse($start_date)->addDays($faker->numberBetween(0, 30))->format('Y-m-d');
    
    $test_start_date = isset($custom_data['assessment_start_date']) ? $custom_data['assessment_start_date'] : \Carbon\Carbon::parse($end_date)->addDays($faker->numberBetween(0, 6))->format('Y-m-d');
    $test_end_date = isset($custom_data['assessment_end_date']) ? $custom_data['assessment_end_date'] : \Carbon\Carbon::parse($test_start_date)->addDays($faker->numberBetween(0, 3))->format('Y-m-d');
    
    $no_of_trainees = isset($custom_data['no_of_trainees']) ? $custom_data['no_of_trainees'] : $faker->numberBetween(2, 30);
    $no_of_absentees = $faker->numberBetween(0, $no_of_trainees);
    $no_of_attendees = $faker->numberBetween(0, ($no_of_trainees-$no_of_absentees));
    //$no_of_failure = $faker->numberBetween(0, $no_of_attendees);

    return [

        'start_date'=> $start_date,
        'end_date' => $end_date,
        'assessment_start_date' => $test_start_date,
        'assessment_end_date'  => $test_end_date,
        'no_of_trainees' => $no_of_trainees,
        'remarks' => $faker->realText(255),
        'should_check_deconflict' => $faker->randomElement(['Yes','No']),
        //'no_of_absentees' => $no_of_absentees,
        'no_of_attendees' => $no_of_attendees,
        'summary_uploaded' => $faker->randomElement(['Yes','No']),
        'admin' => $faker->randomFloat(2, 0, 100),
        'trainer_delivery' => $faker->randomFloat(2, 0, 100),
        'content_relevance' => $faker->randomFloat(2, 0, 100),
        'site_visits' => $faker->randomFloat(2, 0, 100),
        'facilities' => $faker->randomFloat(2, 0, 100),
        //'no_of_failure' => $no_of_failure
    ];
    
});
