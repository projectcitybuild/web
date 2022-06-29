<?php

namespace Database\Factories;

use Entities\Models\Eloquent\GameUnban;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameUnbanFactory extends Factory
{
    protected $model = GameUnban::class;

    public function definition(): array
    {
        return [
            'game_ban_id' => null,
            'staff_player_id' => null,
            'staff_player_type' => null,
        ];
    }

    public function staffPlayer(MinecraftPlayer|Factory $player): GameUnbanFactory
    {
        return $this->for($player, 'staffPlayer');
    }
}
