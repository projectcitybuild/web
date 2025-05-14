<?php

namespace Database\Factories;

use App\Models\MinecraftHome;

class MinecraftHomeFactory extends Factory
{
    protected $model = MinecraftHome::class;

    public function definition()
    {
        return [
            'name' => strtolower($this->faker->unique()->name),
            'world' => $this->faker->randomElement(['creative', 'survival', 'monarch']),
            'x' => $this->faker->numberBetween(0, 20000),
            'y' => $this->faker->numberBetween(0, 20000),
            'z' => $this->faker->numberBetween(0, 20000),
            'pitch' => $this->faker->randomFloat(nbMaxDecimals: 1, min: 0, max: 100),
            'yaw' => $this->faker->randomFloat(nbMaxDecimals: 1, min: 0, max: 100),
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];
    }
}
