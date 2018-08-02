<?php

use Domains\Modules\Players\Models\MinecraftPlayerAlias;
use Domains\Modules\Servers\Services\Querying\Jobs\CreateMinecraftPlayerJob;
use Domains\Modules\Players\Models\MinecraftPlayer;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(MinecraftPlayerAlias::class, function (Faker\Generator $faker) {
    return [
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
