<?php

use App\User;
use App\Payment;
use App\Currency;
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

$factory->define(Payment::class, function (Faker $faker) {
    $amount = $faker->numberBetween(1, 100000);
    $paid_on = $faker->date('Y-m-d');

    return [
        'currency_id' => factory(Currency::class)->create()->id,
        'payment_name' => $faker->word,
        'amount_due' => $amount,
        'due_on' => $faker->date('Y-m-d', $paid_on),
        'amount_paid' => $amount,
        'paid_on' => $paid_on,
        'created_by' => factory(User::class)->create()->id,
    ];
});
