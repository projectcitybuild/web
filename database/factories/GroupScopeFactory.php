<?php

namespace Database\Factories;

use App\Models\GroupScope;

class GroupScopeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GroupScope::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'scope' => $this->faker->name(),
        ];
    }
}
