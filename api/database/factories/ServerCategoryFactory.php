<?php

namespace Database\Factories;

use App\Models\Eloquent\ServerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerCategoryFactory extends Factory
{
    protected $model = ServerCategory::class;
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'display_order' => $this->faker->randomNumber(),
        ];
    }
}
