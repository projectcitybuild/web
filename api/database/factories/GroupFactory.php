<?php

namespace Database\Factories;

use App\Models\Group;
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
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'alias' => $this->faker->name(),
            'is_default' => false,
            'is_staff' => false,
            'is_admin' => false,
        ];
    }

    /**
     * Sets the group as the default group assigned to members who don't have any other group
     */
    public function member(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'member',
                'is_default' => true,
            ];
        });
    }

    /**
     * Sets the group as the Donor group
     */
    public function donor(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'donator',
            ];
        });
    }

    /**
     * Sets the group as the Admin group
     */
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Administrator',
                'is_staff' => true,
                'is_admin' => true,
                'can_access_panel' => true,
            ];
        });
    }
}
