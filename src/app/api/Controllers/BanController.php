<?php

namespace App\Api\Controllers;

use App\Modules\Bans\Services\BanCreationService;
use App\Modules\Bans\Services\BanAuthorisationService;
use App\Modules\Bans\Services\BanLookupService;
use App\Modules\Bans\Services\BanLoggerService;
use App\Modules\Bans\Resources\GameBanResource;
use App\Modules\Bans\Resources\GameUnbanResource;
use App\Modules\Bans\Exceptions\UserNotBannedException;
use App\Modules\Bans\Transformers\BanResource;
use App\Modules\Servers\Repositories\ServerRepository;
use App\Modules\Servers\Transformers\ServerResource;
use App\Modules\ServerKeys\Exceptions\UnauthorisedKeyActionException;
use App\Modules\Players\Models\MinecraftPlayer;
use App\Modules\Players\Services\MinecraftPlayerLookupService;
use App\Core\Exceptions\ServerException;
use App\Core\Exceptions\BadRequestException;
use App\Core\Helpers\MorphMapHelpers;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Routes\Api\ApiController;

class BanController extends ApiController {
    
    /**
     * @var MinecraftPlayerLookupService
     */
    private $playerLookupService;

    /**
     * @var BanCreationService
     */
    private $banCreationService;

    /**
     * @var BanLookupService
     */
    private $banLookupService;

    /**
     * @var BanAuthorisationService
     */
    private $banAuthService;

    /**
     * @var BanLoggerService
     */
    private $banLoggerService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Validator
     */
    private $validationFactory;


    public function __construct(
        MinecraftPlayerLookupService $playerLookupService,
        BanCreationService $banCreationService,
        BanLookupService $banLookupService,
        BanAuthorisationService $banAuthService,
        BanLoggerService $banLoggerService,
        Connection $connection,
        Validator $validationFactory
    ) {
        $this->playerLookupService  = $playerLookupService;
        $this->banCreationService   = $banCreationService;
        $this->banLookupService     = $banLookupService;
        $this->banAuthService       = $banAuthService;
        $this->banLoggerService     = $banLoggerService;
        $this->connection           = $connection;
        $this->validationFactory    = $validationFactory;
    }

    /**
     * Creates a new player ban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function storeBan(Request $request) {
        $aliasTypeMap = [
            'MINECRAFT_UUID' => MinecraftPlayer::class,
        ];
        $aliasTypeWhitelist = implode(',', array_keys($aliasTypeMap));

        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'player_id'         => 'required|max:60',
            'player_alias'      => 'required',
            'banner_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'banner_id'         => 'required|max:60',
            'reason'            => 'string',
            'expires_at'        => 'integer',
            'is_global_ban'     => 'boolean',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.$aliasTypeWhitelist.']',
        ]);

        if($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $serverKey          = $request->get('key');
        $bannedPlayerType   = $request->get('player_id_type');
        $bannedPlayerId     = $request->get('player_id');
        $bannedAliasAtTime  = $request->get('player_alias');
        $staffPlayerType    = $request->get('banner_id_type');
        $staffPlayerId      = $request->get('banner_id');
        $reason             = $request->get('reason');
        $expiryTimestamp    = $request->get('expires_at');
        $isGlobalBan        = $request->get('is_global_ban', false);

        // verify that this server key is allowed to create the given ban type
        if(!$this->banAuthService->isAllowedToBan($isGlobalBan, $serverKey)) {
            if($isGlobalBan) {
                throw new UnauthorisedKeyActionException('This server key does not have permission to create global bans');
            } else {
                throw new UnauthorisedKeyActionException('This server key does not have permission to create local bans');
            }
        }

        // !!!
        // TODO: move this to a factory
        // !!!
        switch($bannedPlayerType) {
            case 'MINECRAFT_UUID':
                $bannedPlayer = $this->playerLookupService->getOrCreateByUuid($bannedPlayerId);
                break;
            default:
                throw new ServerException('bad_player_identifier', 'Invalid player identifier type');
        }
        switch($staffPlayerType) {
            case 'MINECRAFT_UUID':
                $staffPlayer = $this->playerLookupService->getOrCreateByUuid($staffPlayerId);
                break;
            default:
                throw new ServerException('bad_staff_identifier', 'Invalid staff identifier type');
        }


        $bannedPlayerType   = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$bannedPlayerType]);
        $staffPlayerType    = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$staffPlayerType]);
        

        $this->connection->beginTransaction();
        try {
            $ban = $this->banCreationService->storeBan(
                $serverKey->server_id,
                $bannedPlayer->getKey(),
                $bannedPlayerType,
                $bannedAliasAtTime,
                $staffPlayer->getKey(),
                $staffPlayerType,
                $reason,
                $expiryTimestamp,
                $isGlobalBan
            );

            $this->banLoggerService->logBanCreation(
                $ban->game_ban_id, 
                $serverKey->server_key_id, 
                $request->ip()
            );
            
            $serverKey->touch();

            $this->connection->commit();
            return new GameBanResource($ban);

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    /**
     * Creates a new player unban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function storeUnban(Request $request) {
        $aliasTypeMap = [
            'MINECRAFT_UUID' => MinecraftPlayer::class,
        ];
        $aliasTypeWhitelist = implode(',', array_keys($aliasTypeMap));

        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'player_id'         => 'required',
            'banner_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'banner_id'         => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.$aliasTypeWhitelist.']',
        ]);

        if($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }


        $serverKey          = $request->get('key');
        $bannedPlayerType   = $request->get('player_id_type');
        $bannedPlayerId     = $request->get('player_id');
        $staffPlayerType    = $request->get('banner_id_type');
        $staffPlayerId      = $request->get('banner_id');

        // !!!
        // TODO: move this to a factory
        // !!!
        switch($bannedPlayerType) {
            case 'MINECRAFT_UUID':
                $bannedPlayer = $this->playerLookupService->getOrCreateByUuid($bannedPlayerId);
                break;
            default:
                throw new ServerException('bad_player_identifier', 'Invalid player identifier type');
        }
        switch($staffPlayerType) {
            case 'MINECRAFT_UUID':
                $staffPlayer = $this->playerLookupService->getOrCreateByUuid($staffPlayerId);
                break;
            default:
                throw new ServerException('bad_staff_identifier', 'Invalid staff identifier type');
        }

        $bannedPlayerType   = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$bannedPlayerType]);
        $staffPlayerType    = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$staffPlayerType]);
        
        $activeBan = $this->banLookupService->getActivePlayerBan($bannedPlayer->getKey(), $bannedPlayerType, $serverKey);

        // can't unban a player who isn't banned
        if(is_null($activeBan)) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        if(!$this->banAuthService->isAllowedToUnban($activeBan, $serverKey)) {
            if($activeBan->is_global_ban) {
                throw new UnauthorisedKeyActionException(
                    'no_global_ban_permission', 
                    'This server key does not have permission to remove global bans'
                );
            } else {
                throw new UnauthorisedKeyActionException(
                    'no_local_ban_permission',
                    'this server key does not have permission to remove local bans'
                );
            }
        }

        $this->connection->beginTransaction();
        try {
            $unban = $this->banCreationService->storeUnban(
                $serverKey->server_id,
                $staffPlayer->getKey(),
                $staffPlayerType,
                $activeBan
            );

            $this->banLoggerService->logUnbanCreation(
                $activeBan->getKey(),
                $serverKey->server_key_id, 
                $request->ip()
            );

            $serverKey->touch();

            $this->connection->commit();
            return new GameUnbanResource($unban);

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    /**
     * Gets the ban history of a player
     *
     * @param Request $request
     * 
     * @return void
     */
    public function getUserBanHistory(Request $request) {
        $aliasTypeMap = [
            'MINECRAFT_UUID' => MinecraftPlayer::class,
        ];
        $aliasTypeWhitelist = implode(',', array_keys($aliasTypeMap));

        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'player_id'         => 'required',
        ]);

        if($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $serverKey          = $request->get('key');
        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerType   = $request->get('player_id_type');

        $bannedPlayer = $this->playerLookupService->getByUuid($bannedPlayerId);
        if($bannedPlayer === null) {
            return null;
        }

        $bannedPlayerType   = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$bannedPlayerType]);

        $history = $this->banLookupService->getPlayerBanHistory(
            $bannedPlayer->getKey(),
            $bannedPlayerType,
            $serverKey
        );

        if($history === null) {
            return null;
        }

        return GameBanResource::collection($history);
    }

    /**
     * Gets the current ban status of a player on the current
     * server key's server
     *
     * @param Request $request
     * 
     * @return void
     */
    public function getUserStatus(Request $request) {
        $aliasTypeMap = [
            'MINECRAFT_UUID' => MinecraftPlayer::class,
        ];
        $aliasTypeWhitelist = implode(',', array_keys($aliasTypeMap));

        $validator = $this->validationFactory->make($request->all(), [
            'player_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'player_id'         => 'required',
        ]);

        if($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $serverKey          = $request->get('key');
        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerType   = $request->get('player_id_type');

        $bannedPlayer = $this->playerLookupService->getByUuid($bannedPlayerId);
        if($bannedPlayer === null) {
            return null;
        }

        $bannedPlayerType   = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$bannedPlayerType]);

        $activeBan = $this->banLookupService->getActivePlayerBan(
            $bannedPlayer->getKey(),
            $bannedPlayerType,
            $serverKey
        );

        if($activeBan === null) {
            return null;
        }
        return new GameBanResource($activeBan);
    }

}
