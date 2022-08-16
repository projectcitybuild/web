<?php

namespace Database\Factories;

use Domain\BanAppeals\Entities\BanAppealStatus;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BanAppeal>
 */
class BanAppealFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BanAppeal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'explanation' => $this->faker->paragraph,
            'status' => BanAppealStatus::PENDING,
            'is_account_verified' => false,
            'email' => $this->faker->email,
        ];
    }

    public function withResponse(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'decision_note' => $this->faker->paragraph,
                'decided_at' => now(),
                'decider_player_minecraft_id' => MinecraftPlayer::factory(),
            ];
        });
    }

    public function unbanned(): Factory
    {
        return $this->withResponse()->state(function (array $attributes) {
            return [
                'status' => BanAppealStatus::ACCEPTED_UNBAN,
            ];
        });
    }

    public function tempBanned(): Factory
    {
        return $this->withResponse()->state(function (array $attributes) {
            return [
                'status' => BanAppealStatus::ACCEPTED_TEMPBAN,
            ];
        });
    }

    public function denied(): Factory
    {
        return $this->withResponse()->state(function (array $attributes) {
            return [
                'status' => BanAppealStatus::DENIED,
            ];
        });
    }

    public function withVerifiedAccount()
    {
        return $this->withResponse()->state(function (array $attributes) {
            return [
                'is_account_verified' => true,
                'email' => null,
            ];
        });
    }
}
