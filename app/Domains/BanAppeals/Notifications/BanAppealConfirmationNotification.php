<?php

namespace App\Domains\BanAppeals\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use App\Models\BanAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BanAppealConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private BanAppeal $banAppeal
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'discord'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
            'discord' => 'discord-message',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ban Appeal Submitted')
            ->greeting('Your ban appeal has been received')
            ->line('You will be sent another email when your appeal has been decided on.')
            ->line('You can check your appeal at any time:')
            ->action('Check Appeal', $this->banAppeal->showLink());
    }

    public function toDiscord($notifiable)
    {
        return new DiscordMessage(
            content: 'A new ban appeal has been submitted.',
            embeds: [
                new DiscordEmbed(
                    title: 'Ban Appeal',
                    description: Str::limit($this->banAppeal->explanation, 500),
                    url: route('review.ban-appeals.show', $this->banAppeal),
                    author: new DiscordAuthor(
                        name: $this->banAppeal->gamePlayerBan->bannedPlayer->alias,
                    ),
                    fields: [
                        new DiscordEmbedField(
                            name: 'Banning Staff',
                            value: $this->banAppeal->gamePlayerBan->bannerPlayer->alias ?? 'No Alias',
                        ),
                        new DiscordEmbedField(
                            name: 'Ban Reason',
                            value: $this->banAppeal->gamePlayerBan->reason ?? '-',
                        ),
                    ],
                ),
            ],
        );
    }
}
