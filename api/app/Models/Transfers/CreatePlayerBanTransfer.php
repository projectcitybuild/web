<?php

namespace App\Models\Transfers;

use App\Models\MinecraftUUID;
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
