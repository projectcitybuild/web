<?php

namespace Database\Factories;

use App\Models\StripeProduct;
use Illuminate\Support\Str;

class StripeProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StripeProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price_id' => Str::random(),
            'product_id' => Str::random(),
        ];
    }
}
