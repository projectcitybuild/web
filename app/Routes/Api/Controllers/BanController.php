<?php

namespace App\Routes\Api\Controllers;

use App\Modules\Bans\Services\BanManagementService;
use App\Modules\Bans\Exceptions\{UnauthorisedKeyActionException, UserAlreadyBannedException};
use App\Modules\Bans\Repositories\GameBanRepository;
use App\Modules\Servers\Repositories\ServerRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Bans\Transformers\BanResource;
use App\Modules\Servers\Transformers\ServerResource;
use App\Modules\Users\Transformers\UserAliasResource;
use App\Modules\Users\Exceptions\InvalidAliasTypeException;
use App\Modules\Bans\Services\BanAuthorisationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;
use Carbon\Carbon;

class BanController extends Controller {
    
    /**
     * @var GameUserLookupService
     */
    private $gameUserLookup;

    /**
     * @var BanManagementService
     */
    private $banService;

    /**
     * @var BanAuthorisationService
     */
    private $banAuthService;

    public function __construct(
        GameUserLookupService $gameUserLookup,
        BanManagementService $banService,
        BanAuthorisationService $banAuthService
    ) {
        $this->gameUserLookup = $gameUserLookup;
        $this->banService = $banService;
        $this->banAuthService = $banAuthService;
    }

    /**
     * Creates a new player ban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function storeBan(Request $request, Validator $validationFactory) {
        $validator = $validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required',
            'banner_id_type'    => 'required',
            'banner_id'         => 'required',
            'reason'            => 'nullable|string',
            'expires_at'        => 'integer',
            'is_global_ban'     => 'boolean',
        ])->validate();

        $serverKey          = $request->get('key');
        $playerIdType       = $request->get('player_id_type');
        $playerId           = $request->get('player_id');
        $staffIdType        = $request->get('banner_id_type');
        $staffId            = $request->get('banner_id');
        $reason             = $request->get('reason');
        $expiryTimestamp    = $request->get('expires_at');
        $isGlobalBan        = $request->get('is_global_ban', false);

        $this->banAuthService->validateBan($isGlobalBan, $serverKey);

        try {
            $playerGameUser = $this->gameUserLookup->getOrCreateGameUser($playerIdType, $playerId);
            $staffGameUser = $this->gameUserLookup->getOrCreateGameUser($staffIdType, $staffId);

            $ban = $this->banService->storeBan(
                $serverKey,
                $playerGameUser->game_user_id,
                $staffGameUser->game_user_id,
                $reason,
                $expiryTimestamp,
                $isGlobalBan
            );

            return response()->json([
                'status_code' => 200,
                'data' => [
                    'ban' => $ban,
                ],
            ]);

        } catch(InvalidAliasTypeException $e) {
            abort(400, $e->getMessage());
        
        } catch(UnauthorisedKeyActionException $e) {
            abort(401, $e->getMessage());
        
        } catch(UserAlreadyBannedException $e) {
            abort(400, $e->getMessage());
        }
    }

    /**
     * Creates a new player unban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function storeUnban(Request $request, Validator $validationFactory) {
        $validator = $validationFactory->make($request->all(), [
            'player_id_type'    => 'required',
            'player_id'         => 'required',
            'banner_id_type'    => 'required',
            'banner_id'         => 'required',
        ])->validate();

        $serverKey          = $request->get('key');
        $playerIdType       = $request->get('player_id_type');
        $playerId           = $request->get('player_id');
        $staffIdType        = $request->get('banner_id_type');
        $staffId            = $request->get('banner_id');
        
        try {
            $playerGameUserId = $this->gameUserLookup->getOrCreateGameUser($playerIdType, $playerId)->game_user_id;
            $staffGameUserId  = $this->gameUserLookup->getOrCreateGameUser($staffIdType, $staffId)->game_user_id;
        
            $unban = $this->banService->storeUnban($serverKey, $playerGameUserId, $staffGameUserId);

            return response()->json([
                'status_code' => 200,
                'data' => [
                    'unban' => $unban,
                ],
            ]);

        } catch(InvalidAliasTypeException $e) {
            abort(400, $e->getMessage());
        
        } catch(UnauthorisedKeyActionException $e) {
            abort(401, $e->getMessage());
        
        } catch(UserAlreadyBannedException $e) {
            abort(400, $e->getMessage());
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
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'message'       => 'Invalid or malformed input',
                'status_code'   => 400,
                'errors'        => $validator->errors(),
            ]);
        }

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


    public function getBanList(Request $request, GameBanRepository $banRepository, ServerRepository $serverRepository, UserAliasRepository $aliasRepository) {
        $page   = $request->input('page', 1);
        $take   = $request->input('take', 50);
        $offset = $request->input('offset', ($page - 1) * $take);

        $sort = [
            'field' => $request->input('sort_field', 'created_at'),
            'order' => $request->input('sort_direction', 'DESC'),
        ];

        $filter = [];
        if($playerAliasFilter = $request->input('player_alias_at_ban')) {
            $filter['player_alias_at_ban'] = $playerAliasFilter;
        }
        if($bannedAliasFilter = $request->input('banned_alias')) {
            $filter['banned_alias'] = $bannedAliasFilter;
        }
        

        $bans = $banRepository->getBans($take, $offset, $sort, $filter);
        $banCount = $banRepository->getBanCount();

        // normalize servers and users
        $serverIds = $bans->pluck('server_id')->unique()->toArray();
        $servers = $serverRepository->getServersByIds($serverIds);

        $bannedAliasIds = $bans->pluck('banned_alias_id')->unique()->toArray();
        $aliases = $aliasRepository->getAliasesByIds($bannedAliasIds);

        
        return response()->json([
            'status_code' => 200,
            'data' => BanResource::collection($bans),
            'relations' => [
                'servers' => ServerResource::collection($servers->keyBy('server_id')),
                'aliases' => UserAliasResource::collection($aliases->keyBy('user_alias_id')),
            ],
            'meta' => [
                'count' => $banCount,
                'start' => $offset,
                'end'   => min($offset + $take, $banCount),
            ],
        ]);
    }
}
