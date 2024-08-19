<?php

namespace Database\Factories;

use App\Core\Domains\Tokens\TokenGenerator;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\App;

class PasswordResetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PasswordReset::class;

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
