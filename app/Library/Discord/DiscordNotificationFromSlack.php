<?php

namespace App\Library\Discord;

use Illuminate\Notifications\Messages\SlackMessage;

/**
 * Allows a notification to send to a Discord channel using its slack channel functionality.
 */
trait DiscordNotificationFromSlack
{
    public function toDiscord(): SlackMessage
    {
        return $this->toSlack();
    }
}
