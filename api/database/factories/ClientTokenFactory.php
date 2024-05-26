<?php

namespace Database\Factories;

use App\Models\ClientToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client' => $this->faker->name(),
            'token' => Str::random(32),
            'scope' => $this->faker->name(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    public function token(string $token): ClientTokenFactory
    {
        return $this->state(function (array $attributes) use ($token) {
            return ['token' => $token];
        });
    }

    public function scoped(string $scope): ClientTokenFactory
    {
        return $this->state(function (array $attributes) use ($scope) {
            return ['scope' => $scope];
        });
    }
}
