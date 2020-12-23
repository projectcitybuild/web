<?php


namespace App\Library\Backup;


use Spatie\Backup\Notifications\Notifications\BackupHasFailed as BaseBackupHasFailed;

class BackupHasFailed extends BaseBackupHasFailed
{
    use DiscordNotificationFromSlack;
}
