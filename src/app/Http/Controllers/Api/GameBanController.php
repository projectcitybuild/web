<?php

namespace App\Http\Controllers\Api;

use App\Entities\Bans\Resources\GameBanResource;
use App\Entities\Bans\Resources\GameUnbanResource;
use App\Services\PlayerBans\PlayerBanService;
use App\Services\PlayerBans\PlayerUnbanService;
use App\Services\PlayerBans\PlayerBanLookupService;
use App\Entities\GameIdentifierType;
use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Illuminate\Http\Request;
use App\Services\PlayerBans\ServerKeyAuthService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class GameBanController extends ApiController
{
    /**
     * @var PlayerBanService
     */
    private $playerBanService;

    /**
     * @var PlayerUnbanService
     */
    private $playerUnbanService;

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
        PlayerUnbanService $playerUnbanService,
        PlayerBanLookupService $playerBanLookupService,
        ServerKeyAuthService $serverKeyAuthService
    ) {
        $this->playerBanService = $playerBanService;
        $this->playerUnbanService = $playerUnbanService;
        $this->playerBanLookupService = $playerBanLookupService;
        $this->serverKeyAuthService = $serverKeyAuthService;
    }

    /**
     * Creates a new player ban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function storeBan(Request $request)
    {        
        $authHeader = $request->header('Authorization');
        $serverKey = $this->serverKeyAuthService->getServerKey($authHeader);

        $idTypeWhitelist = implode(',', array_keys($this->identifierMapping));

        $validator = Validator::make($request->all(), [
            'player_id_type'    => ['required', Rule::in($idTypeWhitelist)],
            'player_id'         => 'required|max:60',
            'player_alias'      => 'required',
            'staff_id_type'     => ['required', Rule::in($idTypeWhitelist)],
            'staff_id'          => 'required|max:60',
            'reason'            => 'string',
            'expires_at'        => 'integer',
            'is_global_ban'     => 'boolean',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.$idTypeWhitelist.']',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerAlias  = $request->get('player_alias');
        $staffPlayerId      = $request->get('staff_id');
        $reason             = $request->get('reason');
        $expiresAt          = $request->get('expires_at');
        $isGlobalBan        = $request->get('is_global_ban', false);
        
        $bannedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));
        $staffPlayerType    = GameIdentifierType::fromRawValue($request->get('staff_id_type'));

        $ban = $this->playerBanService->ban(
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

    /**
     * Creates a new player unban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function storeUnban(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $serverKey = $this->serverKeyAuthService->getServerKey($authHeader);

        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required',
            'banner_id_type'    => 'required',
            'banner_id'         => 'required',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $bannedPlayerId     = $request->get('player_id');
        $staffPlayerId      = $request->get('staff_id');
        $bannedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));
        $staffPlayerType    = GameIdentifierType::fromRawValue($request->get('staff_id_type'));

        $unban = $this->playerUnbanService->unban($bannedPlayerId,
                                                  $bannedPlayerType->playerType(),
                                                  $staffPlayerId,
                                                  $staffPlayerType->playerType());
        return new GameUnbanResource($unban);
    }

    public function getPlayerStatus(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $serverKey = $this->serverKeyAuthService->getServerKey($authHeader);

        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerType   = GameIdentifierType::fromRawValue($request->get('player_id_type'));

        $activeBan = $this->playerBanLookupService->getStatus($bannedPlayerType->playerType(), $bannedPlayerId);

        if ($activeBan === null) {
            return [
                'data' => null,
            ];
        }
        return new GameBanResource($activeBan);
    }
}
