<?php

namespace App\Domains\MinecraftEventBus\Events;

use App\Models\MinecraftConfig;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MinecraftConfigUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public MinecraftConfig $config,
    ) {}
}
