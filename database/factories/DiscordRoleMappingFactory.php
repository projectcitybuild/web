<?php

namespace Database\Factories;

use App\Models\DiscordRoleMapping;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscordRoleMapping>
 */
class DiscordRoleMappingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guild_id' => fake()->numerify('##################'),
            'role_id' => Role::factory(),
            'discord_role_id' => fake()->numerify('##################'),
            'enabled' => true,
        ];
    }
}
