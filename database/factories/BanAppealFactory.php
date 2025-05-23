<?php

namespace Database\Factories;

use App\Domains\BanAppeals\Data\BanAppealStatus;
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
            'minecraft_uuid' => $this->faker->uuid,
            'date_of_ban' => $this->faker->dateTime,
            'ban_reason' => $this->faker->paragraph,
            'unban_reason' => $this->faker->paragraph,
            'status' => BanAppealStatus::PENDING,
            'email' => $this->faker->email,
        ];
    }

    public function withResponse(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'decision_note' => $this->faker->paragraph,
                'decided_at' => now()->addDays(rand(0, 30)),
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
