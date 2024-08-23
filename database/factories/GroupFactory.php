<?php

namespace Database\Factories;

use App\Models\Group;

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
            'discourse_name' => $this->faker->randomLetter(),
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
                'discourse_name' => 'member',
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
                'discourse_name' => 'donator',
            ];
        });
    }

    /**
     * Sets the group as the Admin group
     */
    public function administrator(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Administrator',
                'discourse_name' => 'administrator',
                'is_staff' => true,
                'is_admin' => true,
                'can_access_panel' => true,
            ];
        });
    }
}
