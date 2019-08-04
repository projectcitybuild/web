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

    /**
     * Maps game identifier types (eg. MINECRAFT_UUID) to game player type (eg. MINECRAFT)
     *
     * @var array
     */
    private $identifierMapping = [
        GameIdentifierType::MinecraftUUID => MinecraftPlayer::class,
    ];


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

    private function getIdTypeWhitelist() : string
    {
        return implode(',', array_keys($this->identifierMapping));
    }

    /**
     * Creates a new player unban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function store(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $this->validateRequest($request->all(), [
            'player_id_type'    => ['required', Rule::in($this->getIdTypeWhitelist())],
            'player_id'         => 'required',
            'banner_id_type'    => ['required', Rule::in($this->getIdTypeWhitelist())],
            'banner_id'         => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.$this->getIdTypeWhitelist().']',
        ]);

        $bannedPlayerId   = $request->get('player_id');
        $staffPlayerId    = $request->get('staff_id');

        $bannedPlayerType = GameIdentifierType::fromRawValue($request->get('player_id_type'));
        $staffPlayerType  = GameIdentifierType::fromRawValue($request->get('staff_id_type'));

        $unban = $this->playerBanService->unban(
            $bannedPlayerId,
            $bannedPlayerType->playerType(),
            $staffPlayerId,
            $staffPlayerType->playerType()
        );
        return new GameUnbanResource($unban);
    }

}
