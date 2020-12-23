<?php

namespace Database\Factories;

use App\Entities\Groups\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'alias' => $this->faker->name(),
            'is_default' => false,
            'is_staff' => false,
            'is_admin' => false,
            'discourse_name' => $this->faker->name(),
        ];
    }

    /**
     * Sets the group as the default group assigned to members who don't yet have one
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function isDefault()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_default' => true,
            ];
        });
    }
}
