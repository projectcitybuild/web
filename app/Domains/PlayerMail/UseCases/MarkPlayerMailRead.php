<?php

namespace App\Domains\PlayerMail\UseCases;

use App\Models\MinecraftPlayer;
use App\Models\PlayerMailRecipient;

class MarkPlayerMailRead
{
    public function execute(MinecraftPlayer $player, int $mailId): PlayerMailRecipient
    {
        $recipient = $player->mailRecipients()
            ->where('mail_id', $mailId)
            ->firstOrFail();

        $player->mailRecipients()
            ->whereKey($recipient->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $recipient->refresh()->load('mail');
    }
}
