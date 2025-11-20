<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Data\UnbanType;

class DeleteIPBan
{
    public function __construct(
        readonly string $ip,
        readonly MinecraftUUID $unbannerUuid,
        readonly UnbanType $unbanType,
    ) {}
}
