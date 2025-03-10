<?php

namespace Database\Factories;

use App\Domains\Bans\Data\UnbanType;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;

class GamePlayerBanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GamePlayerBan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'banned_alias_at_time' => $this->faker->name,
            'reason' => $this->faker->sentence,
            'additional_info' => rand(0, 1) === 1 ? $this->faker->paragraph : null,
            'created_at' => $this->faker->dateTimeBetween('-5 years'),
        ];
    }

    /**
     * Marks the ban as temporary
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function temporary()
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->addDays(rand(1, 365)),
            ];
        });
    }

    /**
     * Indicates that this ban has already expired.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->subDay(),
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'unbanned_at' => now()->subDay(),
                'unban_type' => UnbanType::MANUAL,
            ];
        });
    }

    public function bannedByConsole(): GamePlayerBanFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'banner_player_id' => null,
            ];
        });
    }

    public function bannedBy(MinecraftPlayer|Factory|null $minecraftPlayer): GamePlayerBanFactory
    {
        if (is_null($minecraftPlayer)) {
            return $this->state(function (array $attributes) {
                return ['banner_player_id' => null];
            });
        } else {
            return $this->for($minecraftPlayer, 'bannerPlayer');
        }
    }

    public function bannedPlayer(MinecraftPlayer|Factory $player): GamePlayerBanFactory
    {
        return $this->for($player, 'bannedPlayer');
    }
}
