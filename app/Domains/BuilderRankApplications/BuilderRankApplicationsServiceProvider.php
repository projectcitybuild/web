<?php

namespace App\Domains\BuilderRankApplications;

use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppSubmittedNotification;
use App\Models\BuilderRankApplication;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class BuilderRankApplicationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        Event::listen(NotificationSent::class, function (NotificationSent $event) {
            if (
                $event->notifiable instanceof BuilderRankApplication &&
                $event->notification instanceof BuilderRankAppSubmittedNotification
            ) {
                $event->notifiable->update([
                    'last_notified_at' => now(),
                ]);
            }
        });
    }
}
