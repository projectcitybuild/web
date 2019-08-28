<?php

use App\Entities\Eloquent\Players\Models\MinecraftPlayerAlias;
use App\Entities\Eloquent\Servers\Services\Querying\Jobs\CreateMinecraftPlayerJob;
use App\Entities\Eloquent\Players\Models\MinecraftPlayer;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(MinecraftPlayerAlias::class, function (Faker\Generator $faker) {
    $player = factory(MinecraftPlayer::class)->create();
    return [
        'player_minecraft_id' => $player->player_minecraft_id,
        'alias' => $faker->userName,
        'registered_at' => $faker->dateTimeBetween('-180days', '-1days'),
    ];
});

$factory->state(CreateMinecraftPlayerJob::class, 'withPlayer', function (Faker\Generator $faker) {
    $player = factory(MinecraftPlayer::class)->create();
    return [
        'player_minecraft_id' => $player->player_minecraft_id,
    ];
});
