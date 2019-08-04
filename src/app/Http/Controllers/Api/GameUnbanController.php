<?php

namespace App\Http\Controllers\Api;

use App\Entities\Bans\Resources\GameUnbanResource;
use App\Entities\GameIdentifierType;
use App\Http\ApiController;
use App\Services\PlayerBans\ServerKeyAuthService;
use App\Entities\ServerKeys\Models\ServerKey;
use App\Http\Requests\Api\GameUnbanStoreRequest;
use App\Http\Actions\GameBans\CreatePlayerUnban;
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
     * Creates a new player unban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function store(GameUnbanStoreRequest $request, CreatePlayerUnban $createPlayerUnban)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $input = $request->validated();

        $bannedPlayerId   = $input['player_id'];
        $staffPlayerId    = $input['staff_id'];
        $bannedPlayerType = $input['player_id_type'];
        $staffPlayerType  = $input['staff_id_type'];

        $bannedPlayerType = GameIdentifierType::fromRawValue($bannedPlayerType);
        $staffPlayerType  = GameIdentifierType::fromRawValue($staffPlayerType);

        $unban = $createPlayerUnban->execute(
            $bannedPlayerId,
            $bannedPlayerType->playerType(),
            $staffPlayerId,
            $staffPlayerType->playerType()
        );
        return new GameUnbanResource($unban);
    }

}
