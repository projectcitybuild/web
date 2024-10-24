<?php

namespace App\Domains\MinecraftEventBus\Jobs;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftEventBus\UseCases\PostEventToServer;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyMinecraftPlayerUpdatedJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Server $server,
        public readonly MinecraftUUID $uuid,
    ) {
        $this->server = $server->withoutRelations();
    }

    public function handle(PostEventToServer $postToServer): void
    {
        $postToServer->send(
            server: $this->server,
            path: 'events/player/sync',
            payload: [
                'uuid' => $this->uuid->trimmed(),
            ],
        );
    }
}
