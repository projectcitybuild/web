<?php

namespace Database\Factories;

use App\Domains\BanAppeals\Entities\BanAppealStatus;
use App\Models\BanAppeal;
use App\Models\MinecraftPlayer;

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
