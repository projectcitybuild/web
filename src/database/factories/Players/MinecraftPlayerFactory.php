<?php

use App\Entities\Players\Models\MinecraftPlayer;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(MinecraftPlayer::class, function (Faker\Generator $faker) {
    $account = factory(\App\Entities\Accounts\Models\Account::class)->create();
    return [
        'account_id' => $account->account_id,
        'uuid' => $faker->uuid,
        'playtime' => $faker->numberBetween(0, 99999),
        'last_seen_at' => $faker->dateTimeBetween('-120days', '-1hours'),
    ];
});
