<?php

namespace App\Domains\MinecraftEventBus\Jobs;

use App\Domains\MinecraftEventBus\UseCases\PostEventToServer;
use App\Models\MinecraftConfig;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyMinecraftConfigUpdatedJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Server $server,
        public MinecraftConfig $config,
    ) {
        $this->server = $server->withoutRelations();
    }

    public function handle(PostEventToServer $postEventToServer): void
    {
        $postEventToServer->send(
            server: $this->server,
            path: 'events/config',
            payload: $this->config->toArray(),
        );
    }
}
