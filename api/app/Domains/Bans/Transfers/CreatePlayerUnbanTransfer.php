<?php

namespace App\Domains\Bans\Transfers;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;

class CreatePlayerUnbanTransfer
{
    public function __construct(
      readonly MinecraftUUID $bannedPlayerUUID,
      readonly ?MinecraftUUID $unbannerPlayerUUID,
      readonly ?String $reason,
    ) {}
}
