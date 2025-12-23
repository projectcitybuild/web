<?php

namespace App\Core\Domains\MinecraftUUID\Data;

class MinecraftUUIDLookup
{
    public function __construct(
        public readonly string $username,
        public readonly MinecraftUUID $uuid,
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
