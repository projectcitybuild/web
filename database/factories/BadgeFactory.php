<?php

namespace Database\Factories;

use App\Models\Badge;

class BadgeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Badge::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'display_name' => $this->faker->name,
            'unicode_icon' => $this->faker->randomLetter,
        ];
    }
}
