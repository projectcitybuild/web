<?php

namespace Database\Factories;

use App\Models\AccountEmailChange;
use Illuminate\Support\Str;

class AccountEmailChangeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountEmailChange::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => Str::random(16),
            'email_previous' => $this->faker->email,
            'email_new' => $this->faker->email,
            'is_previous_confirmed' => false,
            'is_new_confirmed' => false,
        ];
    }
}
