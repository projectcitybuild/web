<?php

namespace App\Core\Domains\Groups;

use App\Models\Group;
use Illuminate\Support\ServiceProvider;

class GroupsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GroupsManager::class, function ($app) {
            return new GroupsManager(
                defaultGroup: Group::where('is_default', true)->first()
                    ?? throw new \Exception('Could not find default group. Is there a group with `is_default=true`?')
            );
        });
    }
}
