<?php

namespace App\Routes\Http\Api\Controllers;

use App\Modules\Bans\Services\BanCreationService;
use App\Modules\Bans\Services\BanAuthorisationService;
use App\Modules\Bans\Services\BanLoggerService;
use App\Modules\Bans\Transformers\BanResource;
use App\Modules\Servers\Repositories\ServerRepository;
use App\Modules\Servers\Transformers\ServerResource;
use App\Modules\ServerKeys\Exceptions\UnauthorisedKeyActionException;
// use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Users\Transformers\UserAliasResource;
use App\Modules\Users\UserAliasTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;
use Carbon\Carbon;
use Illuminate\Database\Connection;
use App\Shared\Exceptions\BadRequestException;
use App\Routes\Api\ApiController;
use App\Modules\Players\Models\MinecraftPlayer;
use App\Shared\Helpers\MorphMapHelpers;
use App\Modules\Players\Services\MinecraftPlayerLookupService;
use App\Shared\Exceptions\ServerException;
use App\Modules\Bans\Services\BanLookupService;

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

            // !!!
            // TODO: refactor this to a shared JSON-API output formatter
            // !!!
            return response()->json([
                'status_code' => 200,
                'data' => [
                    'ban' => $ban,
                ],
            ]);

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

            // !!!
            // TODO: refactor this to a shared JSON-API output formatter
            // !!!
            return response()->json([
                'status_code' => 200,
                'data' => [
                    'unban' => $unban,
                ],
            ]);

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    /**
     * Checks whether a player is currently banned on the server key's server
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function checkUserStatus(Request $request, Validator $validationFactory) {
        $validator = $validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required',
        ])->validate();

        $serverKey      = $request->get('key');
        $playerIdType   = $request->get('player_id_type');
        $playerId       = $request->get('player_id');

        $playerGameUserId = $this->gameUserLookup->getOrCreateGameUser($playerIdType, $playerId)->game_user_id;
        $activeBan = $this->banService->getActivePlayerBan($serverKey, $playerGameUserId);

        return response()->json([
            'status_code' => 200,
            'data' => [
                'is_banned' => isset($activeBan),
                'ban'       => $activeBan,
            ],
        ]);
    }

    /**
     * Gets the ban history of a player
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function getUserBanHistory(Request $request, Validator $validationFactory) {
        
    }

}
