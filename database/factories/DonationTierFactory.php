<?php

namespace Database\Factories;

use Entities\Models\Eloquent\DonationTier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DonationTierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DonationTier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::random(10),
            'currency_reward' => $this->faker->randomNumber(3),
        ];
    }
}
