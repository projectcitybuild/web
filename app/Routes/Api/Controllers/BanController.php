<?php

namespace App\Routes\Api\Controllers;

use App\Modules\Bans\Services\BanService;
use App\Modules\Bans\Exceptions\{UnauthorisedKeyActionException, UserAlreadyBannedException};
use App\Modules\Bans\Repositories\GameBanRepository;
use App\Modules\Servers\Repositories\ServerRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Bans\Transformers\BanResource;
use App\Modules\Users\Exceptions\InvalidAliasTypeException;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;
use Carbon\Carbon;

class BanController extends Controller
{
    /**
     * @var GameUserLookupService
     */
    private $gameUserLookup;

    /**
     * @var BanService
     */
    private $banService;

    public function __construct(GameUserLookupService $gameUserLookup, BanService $banService) {
        $this->gameUserLookup = $gameUserLookup;
        $this->banService = $banService;
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
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'message'       => 'Invalid or malformed input',
                'status_code'   => 400,
                'errors'        => $validator->errors(),
            ]);
        }

        $serverKey          = $request->get('key');
        $playerIdType       = $request->get('player_id_type');
        $playerId           = $request->get('player_id');
        $staffIdType        = $request->get('banner_id_type');
        $staffId            = $request->get('banner_id');
        $reason             = $request->get('reason');
        $expiryTimestamp    = $request->get('expires_at');
        $isGlobalBan        = $request->get('is_global_ban', false);
        
        $ban = null;
        try {
            $playerGameUserId = $this->gameUserLookup->getOrCreateGameUserId($playerIdType, $playerId);
            $staffGameUserId  = $this->gameUserLookup->getOrCreateGameUserId($staffIdType, $staffId);
        
            $ban = $this->banService->storeBan(
                $serverKey,
                $playerGameUserId,
                $staffGameUserId,
                $reason,
                $expiryTimestamp,
                $isGlobalBan
            );

        } catch(InvalidAliasTypeException $e) {
            abort(400, $e->getMessage());
        
        } catch(UnauthorisedKeyActionException $e) {
            abort(401, $e->getMessage());
        
        } catch(UserAlreadyBannedException $e) {
            abort(400, $e->getMessage());
        }

        return response()->json([
            'status_code' => 200,
            'data' => [
                'ban' => $ban,
            ],
        ]);
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
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'message'       => 'Invalid or malformed input',
                'status_code'   => 400,
                'errors'        => $validator->errors(),
            ]);
        }

        $serverKey          = $request->get('key');
        $playerIdType       = $request->get('player_id_type');
        $playerId           = $request->get('player_id');
        $staffIdType        = $request->get('banner_id_type');
        $staffId            = $request->get('banner_id');
        
        $unban = null;
        try {
            $playerGameUserId = $this->gameUserLookup->getOrCreateGameUserId($playerIdType, $playerId);
            $staffGameUserId  = $this->gameUserLookup->getOrCreateGameUserId($staffIdType, $staffId);
        
            $unban = $this->banService->storeUnban($serverKey, $playerGameUserId, $staffGameUserId);

        } catch(InvalidAliasTypeException $e) {
            abort(400, $e->getMessage());
        
        } catch(UnauthorisedKeyActionException $e) {
            abort(401, $e->getMessage());
        
        } catch(UserAlreadyBannedException $e) {
            abort(400, $e->getMessage());
        }

        return response()->json([
            'status_code' => 200,
            'data' => [
                'unban' => $unban,
            ],
        ]);
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

        $playerGameUserId = $this->gameUserLookup->getOrCreateGameUserId($playerIdType, $playerId);
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
                'servers' => $servers->keyBy('server_id'),
                'aliases' => $aliases->keyBy('user_alias_id'),
            ],
            'meta' => [
                'count' => $banCount,
                'start' => $offset,
                'end'   => min($offset + $take, $banCount),
            ],
        ]);
    }
}
