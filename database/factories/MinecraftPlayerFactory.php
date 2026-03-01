<?php

namespace Database\Factories;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;

class MinecraftPlayerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MinecraftPlayer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => MinecraftUUID::random()->trimmed(),
            'alias' => $this->faker->userName,
            'nickname' => rand(0, 1) === 1 ? $this->faker->userName : null,
            'muted' => false,
            'walk_speed' => 1.0,
            'fly_speed' => 1.0,
            'sessions' => rand(0, 1000),
            'play_time' => rand(0, 1_000_000_000),
            'afk_time' => rand(0, 600_000_000),
            'blocks_placed' => rand(0, 100_000_000),
            'blocks_destroyed' => rand(0, 100_000_000),
            'blocks_travelled' => rand(0, 100_000_000),
            'last_connected_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
            'last_seen_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    /**
     * Generate a player who does not have a synced time.
     *
     * @return MinecraftPlayerFactory
     */
    public function neverSeen()
    {
        return $this->state(function (array $attributes) {
            return [
                'last_seen_at' => null,
            ];
        });
    }

    public function id(?int $id = null)
    {
        return $this->state(function (array $attributes) use ($id) {
            return [
                'id' => $id ?? $this->faker->randomNumber(),
            ];
        });
    }
}
