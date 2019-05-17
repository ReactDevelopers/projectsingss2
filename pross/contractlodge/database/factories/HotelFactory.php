<?php

use App\Hotel;
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

$factory->define(Hotel::class, function (Faker $faker) {
    return [
		'name' => $faker->bs,
		'address' => $faker->streetAddress,
		'city' => $faker->city,
		'region' => $faker->stateAbbr,
		'postal_code' => $faker->postCode,
		'country_id' => $faker->numberBetween(1, 21),
		'phone' => $faker->tollFreePhoneNumber,
		'email' => $faker->unique()->safeEmail,
		'website' => $faker->domainName,
		'notes' => $faker->paragraph($nbSentences = 3, $variableNbSentences = true),
		'created_by' => 1,
        'deleted_by' => null,
        'deleted_at' => null
    ];
});