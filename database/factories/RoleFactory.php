<?php

namespace Database\Factories;

use App\Models\Role;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'alias' => $this->faker->name(),
            'is_default' => false,
            'is_admin' => false,
            'minecraft_display_name' => $this->faker->name(),
            'minecraft_hover_text' => $this->faker->name(),
        ];
    }

    /**
     * Sets the role as the default role assigned to members who don't have any other role
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

    public function donor(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'donator',
                'role_type' => 'donor',
            ];
        });
    }

    public function administrator(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'administrator',
                'is_admin' => true,
                'role_type' => 'staff',
            ];
        });
    }

    public function staff(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'staff',
                'role_type' => 'staff',
            ];
        });
    }

    public function architect(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'architect',
                'role_type' => 'build',
            ];
        });
    }
}
