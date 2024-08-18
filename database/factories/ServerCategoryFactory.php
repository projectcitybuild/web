<?php

namespace Database\Factories;

use App\Models\ServerCategory;

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
            'name' => $this->faker->word(),
            'display_order' => $this->faker->randomNumber(),
        ];
    }
}
