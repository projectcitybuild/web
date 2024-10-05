<?php

namespace App\Domains\MinecraftRegistration\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftRegistration;
use Illuminate\Support\Str;

class CreateMinecraftRegistration
{
    public function execute(
        MinecraftUUID $minecraftUuid,
        string $minecraftAlias,
        string $email,
    ): MinecraftRegistration {
        MinecraftRegistration::whereUuid($minecraftUuid)->delete();

        return MinecraftRegistration::create([
            'email' => $email,
            'minecraft_uuid' => $minecraftUuid,
            'minecraft_alias' => $minecraftAlias,
            'code' => strtoupper(Str::random(6)),
            'expires_at' => now()->addMinutes(15),
        ]);
    }
}
