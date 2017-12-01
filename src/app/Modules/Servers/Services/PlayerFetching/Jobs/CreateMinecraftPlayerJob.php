<?php

namespace App\Modules\Servers\Services\Querying\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Users\UserAliasTypeEnum;

class CreateMinecraftPlayerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Minecraft in-game name to query
     *
     * @var string
     */
    private $playerName;

    /**
     * Timestamp of the server status request - in case
     * the job needs to be restarted at a later time
     *
     * @var int
     */
    private $requestTime;

    /**
     * Create a new job instance.
     * 
     * @param string $playerName
     * @param int $requestTime
     * @return void
     */
    public function __construct(string $playerName, int $requestTime) {
        $this->playerName = $playerName;
        $this->requestTime = $requestTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    // TODO: move this to PlayerFetching
    public function handle(GameUserLookupService $gameUserLookup) {
        // $uuid = $uuidFetcher->getUuidOf($this->playerName, $this->requestTime);
        
        // if no uuid returned, the Mojang server is probably down
        // if(empty($uuid->getUuid())) {
        //     throw new \Exception('UUID fetch response is empty. Is the Mojang server down?');
        // }

        // $gameUserLookup->getOrCreateGameUser(UserAliasTypeEnum::MINECRAFT_UUID, $uuid->getUuid());
    }
}
