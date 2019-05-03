<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use Illuminate\Http\Request;
use App\Services\PlayerWarnings\PlayerWarningService;
use App\Services\PlayerBans\ServerKeyAuthService;
use App\Services\PlayerBans\Exceptions\UnauthorisedKeyActionException;
use App\Entities\ServerKeys\Models\ServerKey;

final class GameWarningController extends ApiController
{
    /**
     * @var PlayerWarningService
     */
    private $playerWarningService;

    /**
     * @var ServerKeyAuthService
     */
    private $serverKeyAuthService;


    public function __construct(
        PlayerWarningService $playerWarningService,
        ServerKeyAuthService $serverKeyAuthService
    ) {
        $this->playerWarningService = $playerWarningService;
        $this->serverKeyAuthService = $serverKeyAuthService;
    }

    private function getServerKeyFromHeader(Request $request) : ServerKey
    {
        $authHeader = $request->header('Authorization');
        $serverKey = $this->serverKeyAuthService->getServerKey($authHeader);

        return $serverKey;
    }

    public function storeWarning(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        if (!$serverKey->can_warn) {
            throw new UnauthorisedKeyActionException('unauthorised_action', 'This key does not have permission to create warnings');
        }

        $this->validateRequest($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required|max:60',
            'staff_id_type'     => 'required',
            'staff_id'          => 'required|max:60',
            'reason'            => 'string',
            'weight'            => 'integer',
        ]);

        // TODO: use ServerKey
        $serverId = 1;

        $warnedPlayerId     = $request->get('player_id');
        $staffPlayerId      = $request->get('staff_id');
        $reason             = $request->get('reason');
        $weight             = $request->get('weight');
        
        $warnedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));
        $staffPlayerType    = GameIdentifierType::fromRawValue($request->get('staff_id_type'));

        $warning = $this->playerWarningService->warn(
            $warnedPlayerId,
            $warnedPlayerType->playerType(),
            $staffPlayerId,
            $staffPlayerType->playerType(),
            $reason,
            $serverId,
            $weight
        );
        
        // TODO: convert to Resource
        return $warning;
    }

    public function getWarningCount(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        if (!$serverKey->can_warn) {
            throw new UnauthorisedKeyActionException('unauthorised_action', 'This key does not have permission to create warnings');
        }
        
        $this->validateRequest($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required|max:60',
        ]);

        // TODO: use ServerKey
        $serverId = 1;

        $warnedPlayerId     = $request->get('player_id');
        $warnedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));

        $count = $this->playerWarningService->getWarningCount($warnedPlayerId,
                                                              $warnedPlayerType->playerType(),
                                                              $serverId);
        // TODO: convert to Resource
        return $count;
    }
}
