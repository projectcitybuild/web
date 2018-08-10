<?php
namespace Domains\Services\Queries\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Domains\Library\QueryPlayer\PlayerQueryHandler;
use Domains\Modules\Servers\GameTypeEnum;
use Domains\Services\Queries\ServerQueryService;

class PlayerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GameTypeEnum
     */
    private $gameType;

    /**
     * @var int
     */
    private $serverId;

    /**
     * @var array
     */
    private $playerNamesToQuery;

    /**
     * Create a new job instance.
     *
     * @param integer $serverId
     * @param array $playerNamesToQuery
     */
    public function __construct(GameTypeEnum $gameType, int $serverId, array $playerNamesToQuery)
    {
        $this->gameType = $gameType;
        $this->serverId = $serverId;
        $this->playerNamesToQuery = $playerNamesToQuery;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PlayerQueryHandler $playerQueryHandler)
    {
        $playerQueryHandler->setAdapter($gameType->playerQueryAdapter());
        $identifiers = $playerQueryHandler->query($this->playerNamesToQuery);

        ServerQueryService::processPlayerResult($this->gameType, $this->serverId, $identifiers);
    }
}