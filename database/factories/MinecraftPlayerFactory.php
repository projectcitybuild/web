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
            'last_synced_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
            'last_seen_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
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
                'player_minecraft_id' => $id ?? $this->faker->randomNumber(),
            ];
        });
    }
}
