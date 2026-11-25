<?php

namespace Database\Factories;

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerNicknameChange;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MinecraftPlayerNicknameChange>
 */
class MinecraftPlayerNicknameChangeFactory extends Factory
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
            'new_nickname' => fake()->userName(),
            'changed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
