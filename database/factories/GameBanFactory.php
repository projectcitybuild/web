<?php

namespace Database\Factories;

use Domain\Bans\UnbanType;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameBanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameBan::class;

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
            'created_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
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

    public function bannedByConsole(): GameBanFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'staff_player_id' => null,
            ];
        });
    }

    public function bannedBy(MinecraftPlayer|Factory|null $minecraftPlayer): GameBanFactory
    {
        if (is_null($minecraftPlayer)) {
            return $this->state(function (array $attributes) {
                return ['staff_player_id' => null];
            });
        } else {
            return $this->for($minecraftPlayer, 'staffPlayer');
        }
    }

    public function bannedPlayer(MinecraftPlayer|Factory $player): GameBanFactory
    {
        return $this->for($player, 'bannedPlayer');
    }

    public function server(Server|Factory $server): GameBanFactory
    {
        return $this->for($server, 'server');
    }
}
