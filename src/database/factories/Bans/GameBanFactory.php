<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Eloquent\Bans\Models\GameBan;
use App\Entities\Eloquent\Players\Models\MinecraftPlayer;
use App\Entities\Eloquent\Players\Models\MinecraftPlayerAlias;
use App\Entities\Eloquent\Servers\Models\Server;

$factory->define(GameBan::class, function (Faker\Generator $faker) {
    $bannedPlayer = factory(MinecraftPlayer::class)->create();
    $staffPlayer = factory(MinecraftPlayer::class)->create();
    $bannedPlayer->aliases()->save(factory(MinecraftPlayerAlias::class)->make());
    $server = factory(Server::class)->create();

    if (rand(0, 1) == 0) {
        $expiresAt = $faker->dateTimeBetween('-1 years', '+ 1 year');
    } else {
        $expiresAt = null;
    }

    return [
        'server_id' => $server->id,
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
