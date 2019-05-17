<?php

use App\Bill;
use App\User;
use App\Currency;
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

$factory->define(Bill::class, function (Faker $faker) {
    return [
        'race_hotel_id' => factory(RaceHotel::class)->create()->id,
        'created_by' => factory(User::class)->create()->id,
        'contract_signed_on' => $faker->date,
        'currency_id' => factory(Currency::class)->create()->id,
        'exchange_currency_id' => factory(Currency::class)->create()->id,
    ];
});
