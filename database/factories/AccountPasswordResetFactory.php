<?php

namespace Database\Factories;

use Entities\Models\Eloquent\AccountPasswordReset;
use Illuminate\Support\Facades\App;
use Library\Tokens\TokenGenerator;

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
            'token' => App::make(TokenGenerator::class)->make(),
        ];
    }
}
