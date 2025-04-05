<?php

namespace App\Domains\Backup\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Notifications\BaseNotification;

class BackupHasFailedNotification extends BaseNotification
{
    public function __construct(
        public BackupHasFailed $event,
    ) {}

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->error()
            ->from(config('backup.notifications.mail.from.address', config('mail.from.address')), config('backup.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('backup::notifications.backup_failed_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.backup_failed_body', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.exception_message', ['message' => $this->event->exception->getMessage()]))
            ->line(trans('backup::notifications.exception_trace', ['trace' => $this->event->exception->getTraceAsString()]));

        $this->backupDestinationProperties()->each(fn ($value, $name) => $mailMessage->line("{$name}: $value"));

        return $mailMessage;
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            embeds: [
                new DiscordEmbed(
                    title: trans('backup::notifications.backup_failed_subject', ['application_name' => $this->applicationName()]),
                    color: 'e32929',
                    author: new DiscordAuthor(
                        name: config('backup.notifications.discord.username'),
                        iconUrl: config('backup.notifications.discord.avatar_url'),
                    ),
                    fields: [
                        new DiscordEmbedField(
                            name: trans('backup::notifications.exception_message_title'),
                            value: $this->event->exception->getMessage(),
                        ),
                    ],
                ),
            ],
        );
    }
}
