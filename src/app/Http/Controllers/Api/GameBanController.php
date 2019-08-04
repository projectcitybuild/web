<?php

namespace App\Http\Controllers\Api;

use App\Entities\Bans\Resources\GameBanResource;
use App\Entities\Bans\Resources\GameUnbanResource;
use App\Services\PlayerBans\PlayerBanService;
use App\Services\PlayerBans\PlayerBanLookupService;
use App\Entities\GameIdentifierType;
use App\Http\ApiController;
use Illuminate\Http\Request;
use App\Services\PlayerBans\ServerKeyAuthService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Entities\ServerKeys\Models\ServerKey;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\Actions\GameBans\CreatePlayerBan;

final class GameBanController extends ApiController
{
    /**
     * @var PlayerBanService
     */
    private $playerBanService;

    /**
     * @var PlayerBanLookupService
     */
    private $playerBanLookupService;

    /**
     * @var ServerKeyAuthService
     */
    private $serverKeyAuthService;


    public function __construct(
        PlayerBanService $playerBanService,
        PlayerBanLookupService $playerBanLookupService,
        ServerKeyAuthService $serverKeyAuthService
    ) {
        $this->playerBanService = $playerBanService;
        $this->playerBanLookupService = $playerBanLookupService;
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
    public function store(Request $request, CreatePlayerBan $createPlayerBan)
    {        
        $serverKey = $this->getServerKeyFromHeader($request);

        $this->validateRequest($request->all(), [
            'player_id_type'    => ['required', Rule::in(GameIdentifierType::identifierMappingStr())],
            'player_id'         => 'required|max:60',
            'player_alias'      => 'required',
            'staff_id_type'     => ['required', Rule::in(GameIdentifierType::identifierMappingStr())],
            'staff_id'          => 'required|max:60',
            'reason'            => 'string',
            'expires_at'        => 'integer',
            'is_global_ban'     => 'required|boolean',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::identifierMappingStr().']',
        ]);

        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerAlias  = $request->get('player_alias');
        $staffPlayerId      = $request->get('staff_id');
        $reason             = $request->get('reason');
        $expiresAt          = $request->get('expires_at');
        $isGlobalBan        = $request->get('is_global_ban', false);
        
        $bannedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));
        $staffPlayerType    = GameIdentifierType::fromRawValue($request->get('staff_id_type'));

        $ban = $createPlayerBan->execute(
            $serverKey,
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

    public function show(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $this->validateRequest($request->all(), [
            'player_id_type'    => ['required', Rule::in(GameIdentifierType::identifierMappingStr())],
            'player_id'         => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::identifierMappingStr().']',
        ]);

        $bannedPlayerId   = $request->get('player_id');
        $bannedPlayerType = GameIdentifierType::fromRawValue($request->get('player_id_type'));

        $activeBan = $this->playerBanLookupService->getStatus($bannedPlayerType->playerType(), $bannedPlayerId);

        if ($activeBan === null) {
            return [
                'data' => null,
            ];
        }
        return new GameBanResource($activeBan);
    }
}
