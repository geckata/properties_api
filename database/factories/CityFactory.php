<?php

use Faker\Generator as Faker;
use App\Models\City;

$factory->define(City::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->city,
    ];
});
