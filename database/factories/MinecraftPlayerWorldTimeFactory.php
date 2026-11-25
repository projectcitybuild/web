<?php

namespace Database\Factories;

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerWorldTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MinecraftPlayerWorldTime>
 */
class MinecraftPlayerWorldTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'player_id' => MinecraftPlayer::factory(),
            'world_uuid' => fake()->uuid(),
            'creative_time' => fake()->numberBetween(0, 100000),
            'survival_time' => fake()->numberBetween(0, 100000),
            'adventure_time' => fake()->numberBetween(0, 100000),
            'spectator_time' => fake()->numberBetween(0, 100000),
        ];
    }
}
