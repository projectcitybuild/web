<?php

namespace App\Routes\Api\Controllers;

use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Bans\Repositories\GameBanRepository;
use App\Modules\Users\Models\UserAliasType;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;
use Carbon\Carbon;

class BanController extends Controller
{
    private $gameUserRepository;
    private $aliasRepository;
    private $banRepository;

    public function __construct(GameUserRepository $gameUserRepository, 
                                UserAliasRepository $aliasRepository, 
                                GameBanRepository $banRepository
                                ) {
        $this->gameUserRepository = $gameUserRepository;
        $this->aliasRepository = $aliasRepository;
        $this->banRepository = $banRepository;
    }


    private $aliasTypeCache = [];

    /**
     * Validates that the given identifier type matches a type stored in our database
     *
     * @param string $identifierType
     * @return UserAliasType
     */
    private function getAliasType(string $identifierType) : UserAliasType {
        if(array_key_exists($identifierType, $this->aliasTypeCache)) {
            return $this->aliasTypeCache[$identifierType];
        }

        $aliasType = $this->aliasRepository->getAliasType($identifierType);
        if(is_null($aliasType)) {
            abort(400, 'Invalid identifier type given ['.$identifierType.']');
        }
        return $aliasType;
    }

    /**
     * Gets the game user id of the given alias, or alternatively
     * creates a new game user and alias if necessary
     *
     * @param int $aliasTypeId
     * @param string $alias
     * @return int  Game user id
     */
    private function getOrCreateGameUserId(int $aliasTypeId, string $alias) : int {
        $playerAlias = $this->aliasRepository->getAlias($aliasTypeId, $alias);
        if(is_null($playerAlias)) {
            $player = $this->gameUserRepository->store();
            $playerId = $player->game_user_id;

            $this->aliasRepository->store(
                $aliasTypeId,
                $playerId, 
                $alias
            );

            return $playerId;
        }

        return $playerAlias->game_user_id;
    }

    /**
     * Creates a new player ban
     *
     * @param Request $request
     * @param Validator $validationFactory
     * @return void
     */
    public function storeBan(Request $request, Validator $validationFactory) {
        $serverToken = $request->get('token');
        $serverKey = $request->get('key');
        
        $playerIdentifierType   = $request->get('player_id_type');
        $playerIdentifier       = $request->get('player_id');
        $staffIdentifierType    = $request->get('banner_id_type');
        $staffIdentifier        = $request->get('banner_id');
        $reason                 = $request->get('reason');
        $expiryTimestamp        = $request->get('expires_at');
        $isGlobalBan            = $request->get('is_global_ban', false);

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

        $playerAliasType = $this->getAliasType($playerIdentifierType);
        $staffAliasType  = $this->getAliasType($staffIdentifierType);

        $playerGameUserId = $this->getOrCreateGameUserId($playerAliasType->user_alias_type_id, $playerIdentifier);
        $staffGameUserId  = $this->getOrCreateGameUserId($staffAliasType->user_alias_type_id, $staffIdentifier);


        // validate intent vs server key permissions
        if($isGlobalBan && !$serverKey->can_global_ban) {
            abort(401, 'This key does not have permission to create a global ban');
        }


        // check for existing active bans
        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverKey->server_id);
        if(isset($existingBan)) {
            abort(400, 'Player is already banned');
        }

        $ban = $this->banRepository->store([
            'server_id'             => $serverKey->server_id,
            'player_game_user_id'   => $playerGameUserId,
            'staff_game_user_id'    => $staffGameUserId,
            'reason'                => $reason,
            'is_active'             => true,
            'is_global_ban'         => $isGlobalBan,
            'expires_at'            => $expiryTimestamp ? Carbon::createFromTimestamp($expiryTimestamp) : null,
        ]);

        $serverKey->touch();

        // TODO: log usage of server key

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
        $serverToken = $request->get('token');
        $serverKey = $request->get('key');
        
        $playerIdentifierType   = $request->get('player_id_type');
        $playerIdentifier       = $request->get('player_id');

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

        $playerAliasType  = $this->getAliasType($playerIdentifierType);
        $playerGameUserId = $this->getOrCreateGameUserId($playerAliasType->user_alias_type_id, $playerIdentifier);

        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverKey->server_id);
        $serverKey->touch();

        // TODO: log usage of server key

        return response()->json([
            'status_code' => 200,
            'data' => [
                'is_banned' => isset($existingBan),
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
