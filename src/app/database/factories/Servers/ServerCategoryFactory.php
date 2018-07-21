<?php

use App\Modules\Servers\Models\ServerCategory;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(ServerCategory::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->word(),
        'display_order' => $faker->randomNumber(),
    ];
});
