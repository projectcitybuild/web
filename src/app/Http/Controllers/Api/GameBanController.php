<?php

namespace App\Http\Controllers\Api;

use App\Entities\Bans\Resources\GameBanResource;
use App\Entities\ServerKeys\Models\ServerKey;
use App\Entities\GameIdentifierType;
use App\Services\PlayerBans\ServerKeyAuthService;
use App\Services\PlayerBans\Exceptions\UnauthorisedKeyActionException;
use App\Http\Actions\GameBans\CreatePlayerBan;
use App\Http\Requests\Api\GameBanStoreRequest;
use App\Http\Requests\Api\GameBanShowRequest;
use App\Http\Actions\GameBans\GetPlayerBans;
use App\Http\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

final class GameBanController extends ApiController
{
    /**
     * @var ServerKeyAuthService
     */
    private $serverKeyAuthService;


    public function __construct(ServerKeyAuthService $serverKeyAuthService) 
    {
        $this->serverKeyAuthService = $serverKeyAuthService;
    }

    private function getServerKeyFromHeader(Request $request) : ServerKey
    {
        $authHeader = $request->header('Authorization');
        $serverKey = $this->serverKeyAuthService->getServerKey($authHeader);

        return $serverKey;
    }

    /**
     * Creates a new player ban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function store(GameBanStoreRequest $request, CreatePlayerBan $createPlayerBan)
    {        
        $serverKey = $this->getServerKeyFromHeader($request);

        $input = $request->validated();

        $bannedPlayerId     = $input['player_id'];
        $bannedPlayerAlias  = $input['player_alias'];
        $staffPlayerId      = $input['staff_id'];
        $reason             = $input['reason'];
        $expiresAt          = $input['expires_at'];
        $isGlobalBan        = $input['is_global_ban'] ?? false;
        
        $bannedPlayerType   = $input['player_id_type'];
        $bannedPlayerType   = GameIdentifierType::fromRawValue($bannedPlayerType);

        $staffPlayerType    = $input['staff_id_type'];
        $staffPlayerType    = GameIdentifierType::fromRawValue($staffPlayerType);

        if ($isGlobalBan && !$serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('limited_key', 'This server key does not have permission to create global bans');
        }

        $ban = $createPlayerBan->execute(
            $serverKey->server_id,
            $bannedPlayerId, 
            $bannedPlayerType->playerType(),
            $bannedPlayerAlias,
            $staffPlayerId,
            $staffPlayerType->playerType(),
            $reason,
            $expiresAt,
            $isGlobalBan
        );
        
        return new GameBanResource($ban);
    }

    public function show(GameBanShowRequest $request, GetPlayerBans $getPlayerBans)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $input = $request->validated();

        $bannedPlayerId   = $input['player_id'];
        $bannedPlayerType = $input['player_id_type'];
        $bannedPlayerType = GameIdentifierType::fromRawValue($bannedPlayerType);

        $getOnlyFirstResult = false;
        $activeBans = $getPlayerBans->execute(
            $getOnlyFirstResult,
            $bannedPlayerId,
            $bannedPlayerType->playerType(),
            $serverKey->server_id
        );

        if ($activeBans === null) {
            return [
                'data' => [],
            ];
        }
        return new GameBanResource::collection($activeBans);
    }
}
