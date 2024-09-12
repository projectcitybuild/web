<?php

namespace Database\Factories;

use App\Models\EmailChange;
use Illuminate\Support\Str;

class EmailChangeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailChange::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => Str::random(16),
            'email' => $this->faker->email,
            'expires_at' => now()->addDay(),
        ];
    }
}
