<?php

namespace Database\Factories;

use App\Entities\Players\Models\MinecraftPlayer;
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
            'uuid' => $this->faker->uuid,
            'playtime' => $this->faker->numberBetween(0, 99999),
            'last_seen_at' => $this->faker->dateTimeBetween('-120days', '-1hours'),
        ];
    }
}
