<?php

use App\Race;
use App\Hotel;
use App\RaceHotel;
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

$factory->define(RaceHotel::class, function (Faker $faker) {
    $max_check_out = $faker->date;
    $max_rooming_list_confirmed = $faker->date;

    return [
        'race_id' => factory(Race::class)->create()->id,
        'hotel_id' => factory(Hotel::class)->create()->id,
        'inventory_currency_id' => 1,
        'inventory_min_check_in' => $faker->date('Y-m-d', $max_check_out),
        'inventory_min_check_out' => $max_check_out,
        'inventory_notes' => $faker->sentence,
        'rooming_list_sent' => $faker->date('Y-m-d', $max_rooming_list_confirmed),
        'rooming_list_confirmed' => $max_rooming_list_confirmed,
    ];
});
