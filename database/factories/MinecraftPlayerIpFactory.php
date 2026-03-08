<?php

namespace Database\Factories;

use App\Models\MinecraftPlayerIp;

class MinecraftPlayerIpFactory extends Factory
{
    protected $model = MinecraftPlayerIp::class;

    public function definition()
    {
        return [
            'ip' => $this->faker->ipv4(),
            'times_connected' => $this->faker->numberBetween(1, 25),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
