<?php

use App\Entities\Eloquent\Servers\Models\Server;
use App\Entities\Eloquent\GameType;
use App\Entities\Eloquent\Servers\Models\ServerCategory;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(Server::class, function (Faker\Generator $faker) {
    return [
        'name'          => $faker->sentence(),
        'ip'            => $faker->ipv4(),
        'port'          => $faker->numberBetween(20, 8000),
        'display_order' => $faker->numberBetween(1, 15),
        'game_type'     => $faker->randomElement(GameType::values()),
        'is_port_visible' => true,
        'is_visible'    => true,
        'is_querying'   => true,
        'server_category_id' => factory(ServerCategory::class)->create()->getKey(),
    ];
});
