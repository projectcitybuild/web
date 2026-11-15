<?php

namespace App\Domains\PlayerMail\UseCases;

use App\Models\MinecraftPlayer;
use App\Models\PlayerMailRecipient;

class MarkPlayerMailUnread
{
    public function execute(MinecraftPlayer $player, int $mailId): PlayerMailRecipient
    {
        $recipient = $player->mailRecipients()
            ->where('mail_id', $mailId)
            ->firstOrFail();

        $player->mailRecipients()
            ->whereKey($recipient->id)
            ->update(['read_at' => null]);

        return $recipient->refresh()->load('mail');
    }
}
