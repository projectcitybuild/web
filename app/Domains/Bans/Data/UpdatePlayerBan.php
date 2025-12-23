<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use Carbon\Carbon;

class UpdatePlayerBan
{
    public function __construct(
        public readonly int $id,
        public readonly MinecraftUUID $bannedUuid,
        public readonly string $bannedAlias,
        public readonly ?MinecraftUUID $bannerUuid,
        public readonly ?string $bannerAlias,
        public readonly string $reason,
        public readonly ?string $additionalInfo,
        public readonly ?Carbon $expiresAt,
        public readonly ?Carbon $createdAt,
        public readonly ?Carbon $unbannedAt,
        public readonly ?MinecraftUUID $unbannerUuid,
        public readonly ?string $unbannerAlias,
        public readonly ?UnbanType $unbanType,
    ) {}
}
