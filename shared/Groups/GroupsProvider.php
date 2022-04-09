<?php

namespace Shared\Groups;

use App\Entities\Models\Eloquent\Group;
use Illuminate\Support\ServiceProvider;
use Shared\ExternalAccounts\ExternalAccountSync;
use Shared\Groups\UseCases\RemoveGroupMemberUseCase;

class GroupsProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GroupsManager::class, function ($app) {
            return new GroupsManager(
                externalAccountSync: $app->make(ExternalAccountSync::class),
                defaultGroup: Group::where('is_default', true)->first()
                    ?? throw new \Exception("Could not find default group. Is there a group with `is_default=true`?")
            );
        });
    }
}
