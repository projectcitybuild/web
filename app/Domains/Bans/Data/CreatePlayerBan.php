<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use Carbon\Carbon;

class CreatePlayerBan
{
    public function __construct(
        readonly MinecraftUUID $bannedUuid,
        readonly ?string $bannedAlias,
        readonly ?MinecraftUUID $bannerUuid,
        readonly ?string $bannerAlias,
        readonly string $reason,
        readonly ?string $additionalInfo = null,
        readonly ?Carbon $expiresAt = null,
        readonly ?Carbon $createdAt = null,
        readonly ?Carbon $unbannedAt = null,
        readonly ?MinecraftUUID $unbannerUuid = null,
        readonly ?string $unbannerAlias = null,
        readonly ?UnbanType $unbanType = null,
    ) {}
}
