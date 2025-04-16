<?php

namespace App\Domains\MinecraftHomes\UseCases;

use App\Domains\MinecraftHomes\Exceptions\HomeAlreadyExistsException;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Validation\ValidationException;

class CreateMinecraftHome
{
    public function __invoke(MinecraftPlayer $player, string $name)
    {
        // TODO: check number of homes

        $exists = MinecraftHome::where('name', $name)
            ->where('player_id', $player->getKey())
            ->exists();

        throw_if($exists, HomeAlreadyExistsException::class);

        $validated['player_id'] = $player->getKey();

        return MinecraftHome::create($validated);
    }
}
