<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ExternalAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalAccount>
 */
class ExternalAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'provider' => ExternalAccount::PROVIDER_DISCORD,
            'provider_user_id' => fake()->numerify('##################'),
            'username' => fake()->userName(),
            'verified_at' => now(),
        ];
    }
}
