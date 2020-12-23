<?php


namespace App\Library\Backup;


use Spatie\Backup\Notifications\Notifications\CleanupHasFailed as BaseCleanupHasFailed;

class CleanupHasFailed extends BaseCleanupHasFailed
{
    use DiscordNotificationFromSlack;
}
