<?php

namespace App\Domains\PlayerMail\UseCases;

use App\Models\MinecraftPlayer;
use App\Models\PlayerMailRecipient;

class GetPlayerMail
{
    public function execute(MinecraftPlayer $player, int $mailId): PlayerMailRecipient
    {
        return $player->mailRecipients()
            ->with('mail')
            ->where('mail_id', $mailId)
            ->firstOrFail();
    }
}
