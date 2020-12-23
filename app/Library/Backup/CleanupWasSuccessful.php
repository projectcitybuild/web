<?php


namespace App\Library\Backup;


use App\Library\Discord\DiscordNotificationFromSlack;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessful as BaseCleanupWasSuccessful;

class CleanupWasSuccessful extends BaseCleanupWasSuccessful
{
    use DiscordNotificationFromSlack;
}
