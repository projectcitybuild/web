<?php

namespace Database\Factories;

use App\Core\Data\GameType;
use App\Models\Server;

class ServerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Server::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'ip' => $this->faker->ipv4(),
            'port' => $this->faker->numberBetween(20, 8000),
            'display_order' => $this->faker->numberBetween(1, 15),
            'game_type' => $this->faker->randomElement(GameType::cases())->value,
            'is_port_visible' => true,
            'is_visible' => true,
            'is_querying' => true,
        ];
    }
}
