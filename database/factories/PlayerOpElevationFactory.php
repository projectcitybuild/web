<?php

namespace Database\Factories;

use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;

class PlayerOpElevationFactory extends Factory
{
    protected $model = PlayerOpElevation::class;

    public function definition()
    {
        $startedAt = $this->faker->dateTime();
        $endedAt = $startedAt->modify('+3 hours');

        return [
            'player_id' => MinecraftPlayer::factory()->create()->id,
            'reason' => $this->faker->sentence(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'started_at' => now()->subtract(1, 'minute'),
                'ended_at' => now()->addYears(10),
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
