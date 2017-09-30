<?php

namespace App\Routes\Api\Controllers;

use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Bans\Repositories\GameBanRepository;
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

        // validate identifier types
        $playerAliasType = $this->aliasRepository->getAliasType($playerIdentifierType);
        if(is_null($playerAliasType)) {
            abort(400, 'Invalid player identifier type');
        }
        
        if($staffIdentifierType === $playerIdentifierType) {
            $staffAliasType = $playerAliasType;
        } else {
            $staffAliasType = $this->aliasRepository->getAliasType($staffIdentifierType);
            if(is_null($staffAliasType)) {
                abort(400, 'Invalid staff identifier type');
            }
        }

        // get or create new game users
        $playerAlias = $this->aliasRepository->getAlias($playerAliasType->user_alias_type_id, $playerIdentifier);
        if(is_null($playerAlias)) {
            $player = $this->gameUserRepository->store();
            $playerGameUserId = $player->game_user_id;

            $this->aliasRepository->store(
                $playerAliasType->user_alias_type_id, 
                $playerGameUserId, 
                $playerIdentifier
            );
        } else {
            $playerGameUserId = $playerAlias->player_game_user_id;
        }

        $staffAlias = $this->aliasRepository->getAlias($staffAliasType->user_alias_type_id, $staffIdentifier);
        if(is_null($staffAlias)) {
            $staff = $this->gameUserRepository->store();
            $staffGameUserId = $staff->game_user_id;

            $this->aliasRepository->store(
                $staffAliasType->user_alias_type_id, 
                $staffGameUserId, 
                $staffIdentifier
            );
        } else {
            $staffGameUserId = $staffAlias->player_game_user_id;
        }


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




        return response()->json([
            'status_code' => 200,
            'entities' => [
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


}
