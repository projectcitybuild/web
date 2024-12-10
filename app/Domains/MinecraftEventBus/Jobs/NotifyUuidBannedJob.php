<?php

namespace App\Domains\MinecraftEventBus\Jobs;

use App\Domains\MinecraftEventBus\UseCases\PostEventToServer;
use App\Models\GamePlayerBan;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyUuidBannedJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Server $server,
        public GamePlayerBan $ban,
    ) {
        $this->server = $server->withoutRelations();
    }

    public function handle(PostEventToServer $postToServer): void
    {
        $this->ban->load('bannedPlayer', 'bannerPlayer');

        $postToServer->send(
            server: $this->server,
            path: 'events/ban/uuid',
            payload: $this->ban->toArray(),
        );
    }
}
