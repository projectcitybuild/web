<?php


namespace App\Library\Backup;


use App\Library\Discord\DiscordNotificationFromSlack;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessful as BaseBackupWasSuccessful;

class BackupWasSuccessful extends BaseBackupWasSuccessful
{
    use DiscordNotificationFromSlack;
}
