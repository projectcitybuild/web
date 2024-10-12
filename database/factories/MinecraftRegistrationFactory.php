<?php

namespace Database\Factories;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftRegistration;

class MinecraftRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MinecraftRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'minecraft_uuid' => new MinecraftUUID($this->faker->uuid),
            'minecraft_alias' => $this->faker->userName,
            'email' => $this->faker->email,
            'code' => '123456',
            'expires_at' => now()->addDay(),
        ];
    }

    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->subDay(),
            ];
        });
    }
}
