<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Models\Eloquent\ServerKey;
use App\Entities\Models\GameIdentifierType;
use App\Entities\Resources\GameBanResource;
use App\Entities\Resources\GameUnbanResource;
use App\Http\ApiController;
use App\Services\PlayerBans\PlayerBanLookupService;
use App\Services\PlayerBans\PlayerBanService;
use App\Services\PlayerBans\ServerKeyAuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class GameBanV1Controller extends ApiController
{
    /**
     * @var PlayerBanService
     */
    private $playerBanService;

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
        PlayerBanLookupService $playerBanLookupService,
        ServerKeyAuthService $serverKeyAuthService
    ) {
        $this->playerBanService = $playerBanService;
        $this->playerBanLookupService = $playerBanLookupService;
        $this->serverKeyAuthService = $serverKeyAuthService;
    }

    /**
     * Creates a new player ban.
     *
     *
     * @return void
     */
    public function storeBan(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $this->validateRequest($request->all(), [
            'player_id_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'player_id' => 'required|max:60',
            'player_alias' => 'required',
            'staff_id_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'staff_id' => 'required|max:60',
            'reason' => 'string',
            'expires_at' => 'integer',
            'is_global_ban' => 'required|boolean',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::allJoined().']',
        ]);

        $bannedPlayerId = $request->get('player_id');
        $bannedPlayerAlias = $request->get('player_alias');
        $staffPlayerId = $request->get('staff_id');
        $reason = $request->get('reason');
        $expiresAt = $request->get('expires_at');
        $isGlobalBan = $request->get('is_global_ban', false);

        $bannedPlayerType = GameIdentifierType::tryFrom($request->get('player_id_type'));
        $staffPlayerType = GameIdentifierType::tryFrom($request->get('staff_id_type'));

        $ban = $this->playerBanService->ban(
            $serverKey,
            $serverKey->server_id,
            $bannedPlayerId,
            $bannedPlayerType->playerType(),
            $bannedPlayerAlias,
            $staffPlayerId,
            $staffPlayerType->playerType(),
            $reason,
            $expiresAt,
            $isGlobalBan
        );

        return new GameBanResource($ban);
    }

    /**
     * Creates a new player unban.
     *
     *
     * @return void
     */
    public function storeUnban(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $this->validateRequest($request->all(), [
            'player_id_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'player_id' => 'required',
            'staff_id_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'staff_id' => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::allJoined().']',
        ]);

        $bannedPlayerId = $request->get('player_id');
        $staffPlayerId = $request->get('staff_id');

        $bannedPlayerType = GameIdentifierType::tryFrom($request->get('player_id_type'));
        $staffPlayerType = GameIdentifierType::tryFrom($request->get('staff_id_type'));

        $unban = $this->playerBanService->unban(
            $bannedPlayerId,
            $bannedPlayerType->playerType(),
            $staffPlayerId,
            $staffPlayerType->playerType()
        );

        return new GameUnbanResource($unban);
    }

    public function getPlayerStatus(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $this->validateRequest($request->all(), [
            'player_id_type' => ['required', Rule::in(GameIdentifierType::allJoined())],
            'player_id' => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.GameIdentifierType::allJoined().']',
        ]);

        $bannedPlayerId = $request->get('player_id');
        $bannedPlayerType = GameIdentifierType::tryFrom($request->get('player_id_type'));

        $activeBan = $this->playerBanLookupService->getStatus($bannedPlayerType->playerType(), $bannedPlayerId);

        if ($activeBan === null) {
            return [
                'data' => null,
            ];
        }

        return new GameBanResource($activeBan);
    }

    private function getServerKeyFromHeader(Request $request): ServerKey
    {
        $authHeader = $request->header('Authorization');

        return $this->serverKeyAuthService->getServerKey($authHeader);
    }
}
