<?php
namespace App\Modules\Bans\Services;

use App\Modules\Bans\Repositories\GameBanLogRepository;
use App\Modules\Bans\BanLogActionEnum;
use App\Modules\Bans\Models\GameBanLog;

class BanLoggerService
{

    /**
     * @var GameBanLogRepository
     */
    private $logRepository;

    public function __construct(GameBanLogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * Logs the creation of a ban by the given server key
     *
     * @param integer $gameBanId
     * @param integer $serverKeyId
     * @param string|null $ip
     *
     * @return GameBanLog
     */
    public function logBanCreation(int $gameBanId, int $serverKeyId, ?string $ip = null) : GameBanLog
    {
        return $this->logRepository->create(
            $gameBanId,
            $serverKeyId,
            BanLogActionEnum::CREATE_BAN,
            $ip
        );
    }

    /**
     * Logs the creation of an unban by the given server key
     *
     * @param integer $gameBanId
     * @param integer $serverKeyId
     * @param string|null $ip
     *
     * @return GameBanLog
     */
    public function logUnbanCreation(int $gameBanId, int $serverKeyId, ?string $ip = null) : GameBanLog
    {
        return $this->logRepository->create(
            $gameBanId,
            $serverKeyId,
            BanLogActionEnum::CREATE_UNBAN,
            $ip
        );
    }
}
