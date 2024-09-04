<?php

namespace Database\Factories;

use App\Core\Domains\Tokens\TokenGenerator;
use App\Models\AccountActivation;
use Illuminate\Support\Facades\App;

class AccountActivationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountActivation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => App::make(TokenGenerator::class)->make(),
            'expires_at' => now()->addDay(),
        ];
    }
}
