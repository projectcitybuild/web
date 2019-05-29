<?php

namespace App\Entities\Bans\Repositories;

use App\Entities\Bans\Models\GameBanLog;

final class GameBanLogRepository
{
    /**
     * @var GameBanLog
     */
    private $model;

    public function __construct(GameBanLog $model)
    {
        $this->model = $model;
    }

    public function create(
        int $gameBanId,
        int $serverKeyId,
        int $banAction,
        ?string $ip
    ) {
        return $this->model->create([
            'game_ban_id'   => $gameBanId,
            'server_key_id' => $serverKeyId,
            'ban_action'    => $banAction,
            'incoming_ip'   => $ip,
        ]);
    }
}
