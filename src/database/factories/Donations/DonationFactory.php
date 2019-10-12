<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Donations\Models\Donation;

$factory->define(Donation::class, function (Faker\Generator $faker) {
    return [
        'amount' => $faker->numberBetween(3, 100),
        'created_at' => $faker->dateTime(),
        'updated_at' => $faker->dateTime(),
    ];
});
