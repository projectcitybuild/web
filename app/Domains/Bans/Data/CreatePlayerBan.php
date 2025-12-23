<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use Carbon\Carbon;

class CreatePlayerBan
{
    public function __construct(
        public readonly MinecraftUUID $bannedUuid,
        public readonly ?string $bannedAlias,
        public readonly ?MinecraftUUID $bannerUuid,
        public readonly ?string $bannerAlias,
        public readonly string $reason,
        public readonly ?string $additionalInfo = null,
        public readonly ?Carbon $expiresAt = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $unbannedAt = null,
        public readonly ?MinecraftUUID $unbannerUuid = null,
        public readonly ?string $unbannerAlias = null,
        public readonly ?UnbanType $unbanType = null,
    ) {}
}
