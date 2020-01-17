<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Order;
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

$factory->define(\App\Order::class, function (Faker $faker) {
    return [
        'id' => rand(0,9),
        'origin_lat' => $faker->latitude,
        'origin_long' => $faker->longitude,
        'destination_lat' => $faker->latitude,
        'destination_long' => $faker->longitude,
        'distance' => rand(0,99999)
    ];
});
