<?php

use App\Client;
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

$factory->define(Client::class, function (Faker $faker) use ($factory) {
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
		'created_by' => $factory->create('App\User')->id,
        'deleted_by' => null
    ];
});