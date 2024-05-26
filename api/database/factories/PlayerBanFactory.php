<?php

namespace Database\Factories;

use App\Domains\Bans\UnbanType;
use App\Models\Player;
use App\Models\PlayerBan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerBanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlayerBan::class;

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
            'created_at' => $this->faker->dateTimeBetween(startDate: '-5 years', endDate: 'now'),
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

    public function bannedByConsole(): PlayerBanFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'banner_player_id' => null,
            ];
        });
    }

    public function bannedBy(Player|Factory|null $minecraftPlayer): PlayerBanFactory
    {
        if (is_null($minecraftPlayer)) {
            return $this->state(function (array $attributes) {
                return ['banner_player_id' => null];
            });
        } else {
            return $this->for($minecraftPlayer, relationship: 'bannerPlayer');
        }
    }

    public function bannedPlayer(Player|Factory $player): PlayerBanFactory
    {
        return $this->for($player, relationship: 'bannedPlayer');
    }
}
