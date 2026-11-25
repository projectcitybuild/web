<?php

namespace Database\Factories;

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerKill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MinecraftPlayerKill>
 */
class MinecraftPlayerKillFactory extends Factory
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
            'victim_player_id' => MinecraftPlayer::factory(),
            'weapon' => fake()->word(),
            'killed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
