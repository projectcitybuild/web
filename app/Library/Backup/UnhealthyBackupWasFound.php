<?php


namespace App\Library\Backup;


use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFound as BaseUnhealthyBackupWasFound;

class UnhealthyBackupWasFound extends BaseUnhealthyBackupWasFound
{
    use DiscordNotificationFromSlack;
}
