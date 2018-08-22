<?php

namespace Interfaces\Api\Controllers;

use Domains\Modules\Bans\Resources\GameBanResource;
use Domains\Modules\Bans\Resources\GameUnbanResource;
use Domains\Services\PlayerBans\PlayerBanService;
use Domains\Services\PlayerBans\PlayerUnbanService;
use Domains\Services\PlayerBans\PlayerBanLookupService;
use Domains\Modules\GameIdentifierType;
use Application\Exceptions\BadRequestException;
use Interfaces\Api\ApiController;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;

class BanController extends ApiController
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


    public function __construct(PlayerBanService $playerBanService,
                                PlayerUnbanService $playerUnbanService,
                                PlayerBanLookupService $playerBanLookupService,
                                Validator $validationFactory) 
    {
        $this->playerBanService = $playerBanService;
        $this->playerUnbanService = $playerUnbanService;
        $this->playerBanLookupService = $playerBanLookupService;
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

        $ban = $this->playerBanService->ban($bannedPlayerId, 
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
        $bannedPlayerType   = new GameIdentifierType($request->get('player_id_type'));
        $staffPlayerType    = new GameIdentifierType($request->get('staff_id_type'));

        $unban = $this->playerUnbanService->unban($bannedPlayerId,
                                                  $bannedPlayerType->playerType(),
                                                  $staffPlayerId,
                                                  $staffPlayerType->playerType());
        return new GameUnbanResource($unban);
    }

    public function getPlayerStatus(Request $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required',
        ]);

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerType   = new GameIdentifierType($request->get('player_id_type'));

        $activeBan = $this->playerBanLookupService->getStatus($bannedPlayerType, $bannedPlayerId);

        if ($activeBan === null) {
            return null;
        }
        return new GameBanResource($activeBan);
    }
}
