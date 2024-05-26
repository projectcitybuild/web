<?php

namespace App\Domains\Bans\Transfers;

use App\Core\MinecraftUUID\MinecraftUUID;
use Illuminate\Support\Carbon;

class CreatePlayerBanTransfer
{
    public function __construct(
      readonly MinecraftUUID $bannedPlayerUUID,
      readonly String $bannedPlayerAlias,
      readonly ?MinecraftUUID $bannerPlayerUUID,
      readonly ?String $reason,
      readonly ?Carbon $expiresAt,
    ) {}
}
