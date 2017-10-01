<?php

namespace App\Routes\Api\Controllers;

use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Bans\Services\BanService;
use App\Modules\Users\Exceptions\InvalidAliasTypeException;
use App\Modules\Bans\Exceptions\UnauthorisedKeyActionException;
use App\Modules\Bans\Exceptions\UserAlreadyBannedException;
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


}
