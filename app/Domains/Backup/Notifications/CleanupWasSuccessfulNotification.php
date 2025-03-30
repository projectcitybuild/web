<?php

namespace App\Domains\Backup\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\Backup\Events\CleanupWasSuccessful;
use Spatie\Backup\Notifications\BaseNotification;

class CleanupWasSuccessfulNotification extends BaseNotification
{
    public function __construct(
        public CleanupWasSuccessful $event,
    ) {}

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->from(config('backup.notifications.mail.from.address', config('mail.from.address')), config('backup.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('backup::notifications.cleanup_successful_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.cleanup_successful_body', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]));

        $this->backupDestinationProperties()->each(function ($value, $name) use ($mailMessage) {
            $mailMessage->line("{$name}: $value");
        });

        return $mailMessage;
    }

    public function toDiscord(): DiscordMessage
    {
        return new DiscordMessage(
            embeds: [
                new DiscordEmbed(
                    title: trans('backup::notifications.cleanup_successful_subject_title'),
                    color: '0b6623',
                    author: new DiscordAuthor(
                        name: config('backup.notifications.discord.username'),
                        iconUrl: config('backup.notifications.discord.avatar_url'),
                    ),
                    fields: $this->backupDestinationProperties()
                        ->map(fn ($value, $key) => new DiscordEmbedField(name: $key, value: $value))
                        ->toArray(),
                ),
            ],
        );
    }
}
