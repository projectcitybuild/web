<?php

namespace App\Domains\Backup\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\Backup\Events\UnhealthyBackupWasFound;
use Spatie\Backup\Notifications\BaseNotification;
use Spatie\Backup\Tasks\Monitor\HealthCheckFailure;

class UnhealthyBackupWasFoundNotification extends BaseNotification
{
    public function __construct(
        public UnhealthyBackupWasFound $event,
    ) {}

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->error()
            ->from(config('backup.notifications.mail.from.address', config('mail.from.address')), config('backup.notifications.mail.from.name', config('mail.from.name')))
            ->subject(trans('backup::notifications.unhealthy_backup_found_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.unhealthy_backup_found_body', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]))
            ->line($this->problemDescription());

        $this->backupDestinationProperties()->each(function ($value, $name) use ($mailMessage) {
            $mailMessage->line("{$name}: $value");
        });

        if ($this->failure()->wasUnexpected()) {
            $mailMessage
                ->line('Health check: '.$this->failure()->healthCheck()->name())
                ->line(trans('backup::notifications.exception_message', ['message' => $this->failure()->exception()->getMessage()]))
                ->line(trans('backup::notifications.exception_trace', ['trace' => $this->failure()->exception()->getTraceAsString()]));
        }

        return $mailMessage;
    }

    public function toDiscord(): DiscordMessage
    {
        $fields = $this->backupDestinationProperties()
            ->map(fn ($value, $key) => new DiscordEmbedField(name: $key, value: $value))
            ->values();

        if ($this->failure()->wasUnexpected()) {
            $fields->push(
                new DiscordEmbedField(
                    name: 'Health Check',
                    value: $this->failure()->healthCheck()->name(),
                ),
            );
            $fields->push(
                new DiscordEmbedField(
                    name: trans('backup::notifications.exception_message_title'),
                    value: $this->failure()->exception()->getMessage(),
                ),
            );
        }

        return new DiscordMessage(
            embeds: [
                new DiscordEmbed(
                    title: trans('backup::notifications.unhealthy_backup_found_subject_title', [
                        'application_name' => $this->applicationName(),
                        'problem' => $this->problemDescription(),
                    ]),
                    color: 'e32929',
                    author: new DiscordAuthor(
                        name: config('backup.notifications.discord.username'),
                        iconUrl: config('backup.notifications.discord.avatar_url'),
                    ),
                    fields: $fields->toArray(),
                ),
            ],
        );
    }

    protected function problemDescription(): string
    {
        if ($this->failure()->wasUnexpected()) {
            return trans('backup::notifications.unhealthy_backup_found_unknown');
        }

        return $this->failure()->exception()->getMessage();
    }

    protected function failure(): HealthCheckFailure
    {
        return $this->event->backupDestinationStatus->getHealthCheckFailure();
    }
}
