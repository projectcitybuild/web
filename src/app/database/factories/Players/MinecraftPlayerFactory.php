<?php

use App\Modules\Players\Models\MinecraftPlayer;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(MinecraftPlayer::class, function (Faker\Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'playtime' => $faker->numberBetween(0, 99999),
        'last_seen_at' => $faker->dateTimeBetween('-120days', '-1hours'),
    ];
});
