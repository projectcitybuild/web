<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;

class CreateIPBan
{
    public function __construct(
        readonly string $ip,
        readonly string $reason,
        readonly MinecraftUUID $bannerUuid,
        readonly string $bannerAlias,
    ) {}
}
