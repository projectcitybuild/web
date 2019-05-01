<?php

namespace App\Http\Controllers\Api;

use App\Entities\Bans\Resources\GameBanResource;
use App\Entities\Bans\Resources\GameUnbanResource;
use App\Entities\Bans\Exceptions\UserNotBannedException;
use App\Entities\ServerKeys\Exceptions\UnauthorisedKeyActionException;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Services\MinecraftPlayerLookupService;
use App\Services\PlayerBans\BanValidator;
use App\Services\PlayerBans\PlayerBanLookupService;
use App\Services\PlayerBans\PlayerBanService;
use App\Services\PlayerBans\PlayerUnbanService;
use App\Services\PlayerBans\ServerKeyAuthService;
use App\Exceptions\Http\ServerException;
use App\Exceptions\Http\BadRequestException;
use Domains\Helpers\MorphMapHelpers;
use Illuminate\Http\Request;
use App\Http\ApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Entities\GameIdentifierType;
use App\Entities\GamePlayerType;

final class BanController extends ApiController
{
    /**
     * @var MinecraftPlayerLookupService
     */
    private $playerLookupService;

    /**
     * @var BanCreationService
     */
    private $banCreationService;

    /**
     * @var PlayerBanService
     */
    private $playerBanService;

    /**
     * @var BanValidator
     */
    private $banValidator;

    /**
     * Maps game identifier types (eg. MINECRAFT_UUID) to game player type (eg. MINECRAFT)
     *
     * @var array
     */
    private $identifierMapping = [
        GameIdentifierType::MinecraftUUID => MinecraftPlayer::class,
    ];


    public function __construct(
        MinecraftPlayerLookupService $playerLookupService,
        PlayerBanService $playerBanService,
        BanLookupService $banLookupService,
        BanValidator $banValidator,
        BanLoggerService $banLoggerService
    ) {
        $this->playerLookupService  = $playerLookupService;
        $this->playerBanService     = $playerBanService;
        $this->banLookupService     = $banLookupService;
        $this->banValidator         = $banValidator;
        $this->banLoggerService     = $banLoggerService;
    }

    private function getPlayerByIdentifier(GameIdentifierType $identifierType, string $identifier) {
        switch ($identifierType) {
            case GameIdentifierType::MinecraftUUID:
                return $this->playerLookupService->getOrCreateByUuid($identifier);
            default:
                return null;
        }
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
        $aliasTypeWhitelist = implode(',', array_keys($this->identifierMapping));
        dd($aliasTypeWhitelist);

        $validator = Validator::make($request->all(), [
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

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $serverKey          = $request->get('key');
        $bannedPlayerIdType = $request->get('player_id_type');
        $bannedPlayerId     = $request->get('player_id');
        $bannedAliasAtTime  = $request->get('player_alias');
        $staffPlayerIdType  = $request->get('banner_id_type');
        $staffPlayerId      = $request->get('banner_id');
        $reason             = $request->get('reason');
        $expiryTimestamp    = $request->get('expires_at');
        $isGlobalBan        = $request->get('is_global_ban', false);

        $bannedPlayerIdType = new GameIdentifierType($bannedPlayerIdType);
        $staffPlayerIdType  = new GameIdentifierType($staffPlayerIdType);

        $bannedPlayerType   = MorphMapHelpers::getMorphKeyOf($this->identifierMapping[$bannedPlayerIdType->valueOf()]);
        $staffPlayerType    = MorphMapHelpers::getMorphKeyOf($this->identifierMapping[$staffPlayerIdType->valueOf()]);


        // if performing a global ban, assert that the key is allowed to do so
        if ($isGlobalBan && !$serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('This server key does not have permission to create global bans');
        }

        $bannedPlayer = $this->getPlayerByIdentifier($bannedPlayerIdType, $bannedPlayerId);
        if ($bannedPlayer === null) {
            throw new BadRequestException('bad_player_identifier', 'Invalid player identifier type');
        }

        $staffPlayer = $this->getPlayerByIdentifier($staffPlayerIdType, $staffPlayerId);
        if ($staffPlayer === null) {
            throw new BadRequestException('bad_staff_identifier', 'Invalid staff identifier type');
        }
        
        DB::beginTransaction();
        try {
            $ban = $this->playerBanService->ban(
                $serverKey->server_id,
                $bannedPlayer->getKey(),
                new GamePlayerType($bannedPlayerType),
                $bannedAliasAtTime,
                $staffPlayer->getKey(),
                new GamePlayerType($staffPlayerType),
                $reason,
                $expiryTimestamp,
                $isGlobalBan
            );  
            
            $serverKey->touch();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
        $aliasTypeMap = [
            GameIdentifierType::MinecraftUUID => MinecraftPlayer::class,
        ];
        $aliasTypeWhitelist = implode(',', array_keys($aliasTypeMap));

        $validator = Validator::make($request->all(), [
            'player_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'player_id'         => 'required',
            'banner_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'banner_id'         => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.$aliasTypeWhitelist.']',
        ]);

        if ($validator->failed()) {
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
        switch ($bannedPlayerType) {
            case 'MINECRAFT_UUID':
                $bannedPlayer = $this->playerLookupService->getOrCreateByUuid($bannedPlayerId);
                break;
            default:
                throw new ServerException('bad_player_identifier', 'Invalid player identifier type');
        }
        switch ($staffPlayerType) {
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
        if (is_null($activeBan)) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        if (!$this->banValidator->isAllowedToUnban($activeBan, $serverKey)) {
            if ($activeBan->is_global_ban) {
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

        DB::beginTransaction();
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

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return new GameUnbanResource($unban);
    }

    /**
     * Gets the ban history of a player
     *
     * @param Request $request
     *
     * @return void
     */
    public function getUserBanHistory(Request $request)
    {
        $aliasTypeMap = [
            'MINECRAFT_UUID' => MinecraftPlayer::class,
        ];
        $aliasTypeWhitelist = implode(',', array_keys($aliasTypeMap));

        $validator = Validator::make($request->all(), [
            'player_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'player_id'         => 'required',
        ]);

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $serverKey          = $request->get('key');
        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerType   = $request->get('player_id_type');

        $bannedPlayer = $this->playerLookupService->getByUuid($bannedPlayerId);
        if ($bannedPlayer === null) {
            return null;
        }

        $bannedPlayerType   = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$bannedPlayerType]);

        $history = $this->banLookupService->getPlayerBanHistory(
            $bannedPlayer->getKey(),
            $bannedPlayerType,
            $serverKey
        );

        if ($history === null) {
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
    public function getUserStatus(Request $request)
    {
        $aliasTypeMap = [
            'MINECRAFT_UUID' => MinecraftPlayer::class,
        ];
        $aliasTypeWhitelist = implode(',', array_keys($aliasTypeMap));

        $validator = Validator::make($request->all(), [
            'player_id_type'    => 'required|in:'.$aliasTypeWhitelist,
            'player_id'         => 'required',
        ]);

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $serverKey          = $request->get('key');
        $bannedPlayerId     = $request->get('player_id');
        $bannedPlayerType   = $request->get('player_id_type');

        $bannedPlayer = $this->playerLookupService->getByUuid($bannedPlayerId);
        if ($bannedPlayer === null) {
            return null;
        }

        $bannedPlayerType   = MorphMapHelpers::getMorphKeyOf($aliasTypeMap[$bannedPlayerType]);

        $activeBan = $this->banLookupService->getActivePlayerBan(
            $bannedPlayer->getKey(),
            $bannedPlayerType,
            $serverKey
        );

        if ($activeBan === null) {
            return null;
        }
        return new GameBanResource($activeBan);
    }
}
