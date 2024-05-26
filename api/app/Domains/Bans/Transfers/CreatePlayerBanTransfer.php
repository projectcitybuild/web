<?php

namespace App\Domains\Bans\Transfers;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use Illuminate\Support\Carbon;

class CreatePlayerBanTransfer
{
    public function __construct(
      readonly MinecraftUUID $bannedPlayerUUID,
      readonly string $bannedPlayerAlias,
      readonly ?MinecraftUUID $bannerPlayerUUID,
      readonly ?string $reason,
      readonly ?Carbon $expiresAt,
    ) {}
}
