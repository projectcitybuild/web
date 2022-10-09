<?php

namespace Entities\Notifications;

use Awssat\Notifications\Messages\DiscordEmbed;
use Awssat\Notifications\Messages\DiscordMessage;
use Entities\Models\Eloquent\ShowcaseApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use function route;

class ShowcaseApplicationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private ShowcaseApplication $showcaseApplication,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     *
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
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Showcase application submitted')
            ->greeting('Hi '.$this->showcaseApplication->account->username.',')
            ->line('Your build has been submitted to the showcase.')
            ->line('The Architect Council will review your submission as soon as possible.')
            ->action(
                text: 'Check Application Status',
                url: route('front.showcase.status', $this->showcaseApplication->getKey()),
            )
            ->line('If you have any questions, please feel free to reach out to staff at any time.');
    }

    public function toDiscord()
    {
        return (new DiscordMessage)
            ->content('A new showcase application has arrived.')
            ->embed(function (DiscordEmbed $embed) {
                $embed->title('Showcase Submission', route('front.panel.showcase-apps.show', $this->showcaseApplication))
                    ->field('Title', $this->showcaseApplication->title)
                    ->field('Description', Str::limit($this->showcaseApplication->description, 500))
                    ->author($this->showcaseApplication->creators);
            });
    }
}
