<?php

namespace Database\Factories;

use App\Entities\Models\Eloquent\AccountPasswordReset;
use App\Helpers\TokenHelpers;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountPasswordResetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountPasswordReset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->email(),
            'token' => TokenHelpers::generateToken(),
        ];
    }
}
