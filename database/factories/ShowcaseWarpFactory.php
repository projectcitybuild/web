<?php

namespace Database\Factories;

use Entities\Models\Eloquent\ShowcaseWarp;

class ShowcaseWarpFactory extends Factory
{
    protected $model = ShowcaseWarp::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->firstName,
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'creators' => $this->faker->name,
            'location_world' => $this->faker->randomElement(['creative', 'survival', 'monarch']),
            'location_x' => $this->faker->numberBetween(0, 20000),
            'location_y' => $this->faker->numberBetween(0, 20000),
            'location_z' => $this->faker->numberBetween(0, 20000),
            'location_pitch' => $this->faker->randomFloat(nbMaxDecimals: 1, min: 0, max: 100),
            'location_yaw' => $this->faker->randomFloat(nbMaxDecimals: 1, min: 0, max: 100),
            'built_at' => $this->faker->dateTime,
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];
    }
}
