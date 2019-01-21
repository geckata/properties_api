<?php

use Faker\Generator as Faker;
use App\Models\City;
use App\Models\Property;

$factory->define(Property::class, function (Faker $faker) {
    $addressLines = explode("\n", $faker->address);
    $addressLines = array_map('trim', $addressLines);

    return [
        'city_id' => factory(City::class)->create()->id,
        'name' => $faker->company,
        'address_line_1' => $addressLines[0],
        'address_line_2' => $addressLines[1] ?? null,
        'postcode' => $faker->postcode,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});

$factory->state(Property::class, 'existing_city', function (\Faker\Generator $faker) {
    $cityId = DB::select(DB::raw('SELECT * FROM `cities` WHERE id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `cities` ) ORDER BY id LIMIT 1'));

    return [
        'city_id' => $cityId[0]->id,
    ];
});
