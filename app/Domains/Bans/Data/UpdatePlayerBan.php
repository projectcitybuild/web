<?php

namespace App\Domains\Bans\Data;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use Carbon\Carbon;

class UpdatePlayerBan
{
    public function __construct(
        readonly int $id,
        readonly MinecraftUUID $bannedUuid,
        readonly string $bannedAlias,
        readonly ?MinecraftUUID $bannerUuid,
        readonly ?string $bannerAlias,
        readonly string $reason,
        readonly ?string $additionalInfo,
        readonly ?Carbon $expiresAt,
        readonly ?Carbon $createdAt,
        readonly ?Carbon $unbannedAt,
        readonly ?MinecraftUUID $unbannerUuid,
        readonly ?string $unbannerAlias,
        readonly ?UnbanType $unbanType,
    ) {}
}
