<?php

namespace Database\Factories;

use App\Models\Eloquent\MinecraftPlayer;
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
            'uuid' => str_replace(search: '-', replace: '', subject: $this->faker->uuid),
            'last_synced_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
            'last_seen_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
        ];
    }
}
