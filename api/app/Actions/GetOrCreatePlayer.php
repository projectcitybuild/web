<?php

namespace App\Actions;

use App\Models\Eloquent\Player;
use App\Models\Events\PlayerCreatedEvent;

class GetOrCreatePlayer
{
    public function run(String $uuid): Player
    {
        $player = Player::uuid($uuid)->first();

        if ($player === null) {
            $player = Player::create(['uuid' => $uuid]);
            PlayerCreatedEvent::dispatch($player);
        }
        return $player;
    }
}
