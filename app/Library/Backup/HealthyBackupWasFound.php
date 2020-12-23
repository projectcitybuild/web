<?php


namespace App\Library\Backup;


use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFound as BaseHealthyBackupWasFound;

class HealthyBackupWasFound extends BaseHealthyBackupWasFound
{
    use DiscordNotificationFromSlack;
}
