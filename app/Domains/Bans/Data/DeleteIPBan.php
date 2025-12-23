<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;

class DeleteIPBan
{
    public function __construct(
        public readonly string $ip,
        public readonly MinecraftUUID $unbannerUuid,
        public readonly UnbanType $unbanType,
    ) {}
}
