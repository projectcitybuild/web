<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;

class CreateIPBan
{
    public function __construct(
        public readonly string $ip,
        public readonly string $reason,
        public readonly MinecraftUUID $bannerUuid,
        public readonly string $bannerAlias,
    ) {}
}
