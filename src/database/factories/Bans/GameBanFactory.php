<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Bans\Models\GameBan;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Servers\Models\Server;

$factory->define(GameBan::class, function (Faker\Generator $faker) {
    $bannedPlayer = MinecraftPlayer::inRandomOrder()->first();
    $staffPlayer = MinecraftPlayer::inRandomOrder()->first();

    if (rand(0, 1) == 0) {
        $expiresAt = $faker->dateTimeBetween('-1 years', '+ 1 year');
    } else {
        $expiresAt = null;
    }

    return [
        'server_id' => Server::inRandomOrder()->first(),
        'banned_player_id' => $bannedPlayer->getKey(),
        'banned_player_type' => 'minecraft_player',
        'banned_alias_at_time' => $bannedPlayer->getBanReadableName(),
        'staff_player_id' => $staffPlayer->getKey(),
        'staff_player_type' => 'minecraft_player',
        'reason' => $faker->sentence,
        'is_active' => $faker->boolean,
        'is_global_ban' => $faker->boolean,
        'expires_at' => $expiresAt,
        'created_at' => $faker->dateTimeBetween('-5 years', 'now')
    ];
});
