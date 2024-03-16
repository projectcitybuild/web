<?php

namespace App\Models\Transfers;

use App\Models\MinecraftUUID;
use Illuminate\Support\Carbon;

class CreatePlayerUnbanTransfer
{
    public function __construct(
      readonly MinecraftUUID $bannedPlayerUUID,
      readonly ?MinecraftUUID $unbannerPlayerUUID,
      readonly ?String $reason,
    ) {}
}
