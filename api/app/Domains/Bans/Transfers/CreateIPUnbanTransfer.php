<?php

namespace App\Domains\Bans\Transfers;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;

class CreateIPUnbanTransfer
{
    public function __construct(
      readonly string $ip,
      readonly ?MinecraftUUID $unbannerPlayerUUID,
    ) {}
}
