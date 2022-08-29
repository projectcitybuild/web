<?php

namespace Database\Factories;

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
            'staff_player_id' => MinecraftPlayer::factory(),
            'reason' => $this->faker->sentence,
            'is_active' => $this->faker->boolean,
            'is_global_ban' => $this->faker->boolean,
            'created_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'expires_at' => null,
        ];
    }

    /**
     * Enables the ban.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Disables the ban.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
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
                'is_active' => false,
                'expires_at' => now()->subDay(),
            ];
        });
    }

    public function bannedBy(MinecraftPlayer $minecraftPlayer): GameBanFactory
    {
        return $this->state(function (array $attributes) use ($minecraftPlayer) {
            return [
                'staff_player_id' => $minecraftPlayer->getKey(),
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

    public function bannedPlayer(MinecraftPlayer|Factory $player): GameBanFactory
    {
        return $this->for($player, 'bannedPlayer');
    }

    public function server(Server|Factory $server): GameBanFactory
    {
        return $this->for($server, 'server');
    }
}
