<?php

namespace App\Domains\Bans\Transfers;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use Illuminate\Support\Carbon;

class CreateIPBanTransfer
{
    public function __construct(
      readonly string $ip,
      readonly ?MinecraftUUID $bannerPlayerUUID,
      readonly ?string $reason,
    ) {}
}
