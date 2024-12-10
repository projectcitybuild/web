<?php

namespace App\Core\Domains\MinecraftUUID\Data;

class MinecraftUUIDLookup
{
    public function __construct(
        readonly string $username,
        readonly MinecraftUUID $uuid,
    ) {}

    public static function fromResponse(array $response): ?self
    {
        if ($response['success'] !== true) {
            return null;
        }

        $player = $response['data']['player'];

        return new MinecraftUUIDLookup(
            username: $player['username'],
            uuid: new MinecraftUUID($player['id']),
        );
    }
}
