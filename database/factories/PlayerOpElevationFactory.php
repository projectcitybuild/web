<?php

namespace Database\Factories;

use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;

class PlayerOpElevationFactory extends Factory
{
    protected $model = PlayerOpElevation::class;

    public function definition()
    {
        return [
            'player_id' => MinecraftPlayer::factory()->create()->id,
            'reason' => $this->faker->text(32),
            'started_at' => $this->faker->dateTime(),
            'ended_at' => $this->faker->dateTime(),
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'ended_at' => now()->addDay(),
            ];
        });
    }

    public function ended(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'ended_at' => now()->subDay(),
            ];
        });
    }
}
