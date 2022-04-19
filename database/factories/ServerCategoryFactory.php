<?php

namespace Database\Factories;

use Entities\Models\Eloquent\ServerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServerCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'  => $this->faker->word(),
            'display_order' => $this->faker->randomNumber(),
        ];
    }
}
