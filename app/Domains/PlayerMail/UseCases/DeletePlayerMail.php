<?php

namespace App\Domains\PlayerMail\UseCases;

use App\Models\MinecraftPlayer;

class DeletePlayerMail
{
    public function execute(MinecraftPlayer $player, int $mailId): void
    {
        $recipient = $player->mailRecipients()
            ->where('mail_id', $mailId)
            ->firstOrFail();

        $recipient->delete();
    }
}
