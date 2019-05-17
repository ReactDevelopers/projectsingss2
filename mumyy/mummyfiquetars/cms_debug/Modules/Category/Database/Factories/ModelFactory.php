<?php

use Modules\Category\Entities\Category;

$factory->define(Category::class, function (Faker\Generator $faker) {
	// name
	// description
	// sorts
	// country_id
	// status

    return [
        'name' => $faker->name,
        // 'email' => $faker->email,
        // 'password' => bcrypt(str_random(10)),
        // 'remember_token' => str_random(10),
    ];
});

// $factory->define(\Modules\Category\Entities\Category::class, function (Faker\Generator $faker) {
//     return [
//         'name' => $faker->name,
//         // 'email' => $faker->email,
//         // 'password' => bcrypt(str_random(10)),
//         // 'remember_token' => str_random(10),
//     ];
// });