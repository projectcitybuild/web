<?php

namespace Database\Factories;

use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'uuid' => str_replace('-', '', $this->faker->uuid),
            'last_synced_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
        ];
    }

    /**
     * Generate a player who does not have a synced time.
     *
     * @return MinecraftPlayerFactory
     */
    public function neverSynced()
    {
        return $this->state(function (array $attributes) {
            return [
                'last_synced_at' => null,
            ];
        });
    }
}
