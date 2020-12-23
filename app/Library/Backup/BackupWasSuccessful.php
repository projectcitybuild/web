<?php


namespace App\Library\Backup;


use Spatie\Backup\Notifications\Notifications\BackupWasSuccessful as BaseBackupWasSuccessful;

class BackupWasSuccessful extends BaseBackupWasSuccessful
{
    use DiscordNotificationFromSlack;
}
