<?php

namespace Database\Factories;

use App\Entities\Donations\Models\Donation;
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
        $donation = Donation::factory()->create();

        return [
            'donation_id' => $donation->getKey(),
            'is_active' => true,
            'is_lifetime_perks' => false,
            'expires_at' => $this->faker->dateTime(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    /**
     * Indicate that the donation will never expire
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function lifetime()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_lifetime_perks' => true,
            ];
        });
    }
}
