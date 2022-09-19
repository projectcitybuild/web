<?php

namespace Entities\Notifications;

use Awssat\Notifications\Messages\DiscordEmbed;
use Awssat\Notifications\Messages\DiscordMessage;
use Entities\Models\Eloquent\BanAppeal;
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
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'discordHook'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
            'discordHook' => 'discord-message',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
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

    public function toDiscord()
    {
        return (new DiscordMessage)
            ->content('A new ban appeal has been submitted.')
            ->embed(function (DiscordEmbed $embed) {
                $embed->title('Ban Appeal', route('front.panel.ban-appeals.show', $this->banAppeal))
                    ->description(Str::limit($this->banAppeal->explanation, 500))
                    ->field('Banning Staff', $this->banAppeal->gamePlayerBan->bannerPlayer->getBanReadableName() ?? 'No Alias')
                    ->field('Ban Reason', $this->banAppeal->gamePlayerBan->reason ?? '-')
                    ->author($this->banAppeal->gamePlayerBan->bannedPlayer->getBanReadableName());
            });
    }
}
