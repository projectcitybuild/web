<?php


namespace App\Library\Backup;


use App\Library\Discord\DiscordNotificationFromSlack;
use Spatie\Backup\Notifications\Notifications\BackupHasFailed as BaseBackupHasFailed;

class BackupHasFailed extends BaseBackupHasFailed
{
    use DiscordNotificationFromSlack;
}
