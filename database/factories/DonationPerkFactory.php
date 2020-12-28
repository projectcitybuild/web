<?php

namespace Database\Factories;

use App\Entities\Donations\Models\DonationPerk;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationPerkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DonationPerk::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'is_active' => true,
            'is_lifetime_perks' => false,
            'expires_at' => $this->faker->dateTime(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    /**
     * Indicate that the donation will never expire
     */
    public function lifetime(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_lifetime_perks' => true,
            ];
        });
    }

    /**
     * Indicate that the donation has already expired
     */
    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_lifetime_perks' => false,
                'expires_at' => now()->subDay(),
            ];
        });
    }

    /**
     * Indicate that the donation will expire in the future
     */
    public function notExpired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_lifetime_perks' => false,
                'expires_at' => now()->addDay(),
            ];
        });
    }
}
