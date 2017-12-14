<?php

use App\Modules\Servers\Models\Server;
use App\Modules\Servers\GameTypeEnum;
use App\Modules\Servers\Models\ServerCategory;

/** 
 * @var \Illuminate\Database\Eloquent\Factory $factory 
 */
$factory->define(Server::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->sentence(),
        'ip'    => $faker->ipv4(),
        'port'  => $faker->numberBetween(20, 8000),
        'server_category_id' => function () {
            return factory(ServerCategory::class)->create()->server_category_id;
        },
        'display_order' => $faker->numberBetween(1, 15),
        'game_type' => $faker->randomElement(GameTypeEnum::getValues()),
    ];
});