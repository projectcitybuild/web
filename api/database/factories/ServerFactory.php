<?php

namespace Database\Factories;

use App\Models\Eloquent\Server;
use App\Models\Eloquent\ServerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'ip' => $this->faker->ipv4(),
            'port' => $this->faker->numberBetween(20, 8000),
            'display_order' => $this->faker->numberBetween(1, 15),
            'game_type' => 1,
            'is_port_visible' => true,
            'is_visible' => true,
            'is_querying' => true,
        ];
    }
}
