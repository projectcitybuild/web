<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\StripeProduct;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-5 years');

        return [
            'stripe_price' => Str::random(),
            'stripe_product' => Str::random(),
            'paid_currency' => $this->faker->randomElement(['aud', 'usd']),
            'paid_unit_amount' => $this->faker->numberBetween(100, 50000),
            'original_currency' => 'aud',
            'original_unit_amount' => $this->faker->numberBetween(100, 50000),
            'unit_quantity' => $this->faker->numberBetween(1, 6),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }

    public function product(StripeProduct $product): Factory
    {
        return $this->state(function (array $attributes) use ($product) {
            return [
                'stripe_price' => $product->price_id,
                'stripe_product' => $product->product_id,
            ];
        });
    }
}
