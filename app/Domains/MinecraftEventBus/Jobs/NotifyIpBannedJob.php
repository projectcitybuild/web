<?php

namespace App\Domains\MinecraftEventBus\Jobs;

use App\Domains\MinecraftEventBus\UseCases\PostEventToServer;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyIpBannedJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Server $server,
        public GameIPBan $ban,
    ) {
        $this->server = $server->withoutRelations();
        $this->ban = $ban->withoutRelations();
    }

    public function handle(PostEventToServer $postEventToServer): void
    {
        $postEventToServer->send(
            server: $this->server,
            path: 'events/ban/ip',
            payload: $this->ban->toArray(),
        );
    }
}
