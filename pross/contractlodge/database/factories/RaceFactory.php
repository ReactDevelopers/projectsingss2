<?php

use App\Race;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Race::class, function (Faker $faker) {
    return [
        'name'          => $faker->bs,
        'year'          => $faker->numberBetween(2019, 2037),
        'start_on'      => $faker->dateTimeBetween('+1 week', '+1 month'),
        'end_on'        => $faker->dateTimeBetween('+2 week', '+1 month'),
        'currency_id'   => $faker->shuffle('1', '2', '3'),
        'created_by'    => 1,
        'deleted_by'    => null,
        'deleted_at'    => null
    ];
});
