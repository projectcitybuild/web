<?php

namespace App\Http\Controllers\Api;

use App\Entities\Bans\Resources\GameBanResource;
use App\Entities\Bans\Resources\GameUnbanResource;
use App\Services\PlayerBans\PlayerBanService;
use App\Services\PlayerBans\PlayerUnbanService;
use App\Services\PlayerBans\PlayerBanLookupService;
use App\Entities\GameIdentifierType;
use App\Exceptions\Http\BadRequestException;
use Interfaces\Api\ApiController;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;
use App\Services\PlayerBans\ServerKeyAuthService;

final class GameBanController extends ApiController
{
    /**
     * @var Validator
     */
    private $validationFactory;

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


    public function __construct(
        PlayerBanService $playerBanService,
        PlayerUnbanService $playerUnbanService,
        PlayerBanLookupService $playerBanLookupService,
        ServerKeyAuthService $serverKeyAuthService,
        Validator $validationFactory
    ) {
        $this->playerBanService = $playerBanService;
        $this->playerUnbanService = $playerUnbanService;
        $this->playerBanLookupService = $playerBanLookupService;
        $this->serverKeyAuthService = $serverKeyAuthService;
        $this->validationFactory    = $validationFactory;
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

        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required|max:60',
            'player_alias'      => 'required',
            'staff_id_type'     => 'required',
            'staff_id'          => 'required|max:60',
            'reason'            => 'string',
            'expires_at'        => 'integer',
            'is_global_ban'     => 'boolean',
        ]);

        if ($validator->failed()) {
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

        $ban = $this->playerBanService->ban($serverKey->server_id,
                                            $bannedPlayerId, 
                                            $bannedPlayerType->playerType(),
                                            $bannedPlayerAlias,
                                            $staffPlayerId,
                                            $staffPlayerType->playerType(),
                                            $reason,
                                            $expiresAt,
                                            $isGlobalBan);
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

        if ($validator->failed()) {
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

        if ($validator->failed()) {
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
