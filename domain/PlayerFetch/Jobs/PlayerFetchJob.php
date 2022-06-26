<?php

namespace Domain\PlayerFetch\Jobs;

use Domain\PlayerFetch\PlayerFetchService;
use Entities\Models\GameType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class PlayerFetchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private GameType $gameType;

    private array $aliases;

    private ?int $timestamp;

    /**
     * Create a new job instance.
     */
    public function __construct(GameType $gameType, array $aliases, ?int $timestamp = null)
    {
        $this->gameType = $gameType;
        $this->aliases = $aliases;
        $this->timestamp = $timestamp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PlayerFetchService $playerFetchService)
    {
        $playerFetchService->fetchSynchronously(
            $this->gameType,
            $this->aliases,
            $this->timestamp
        );
    }
}
