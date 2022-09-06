<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\APIController;
use App\Services\PlayerBans\PlayerBanLookupService;
use App\Services\PlayerBans\PlayerBanService;
use App\Services\PlayerBans\ServerKeyAuthService;
use Entities\Models\Eloquent\ServerKey;
use Entities\Models\PlayerIdentifierType;
use Entities\Resources\GameBanV1Resource;
use Entities\Resources\GameUnbanV1Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @deprecated
 */
final class GameBanV1Controller extends APIController
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
            'player_id_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'player_id' => 'required|max:60',
            'player_alias' => 'required',
            'staff_id_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'staff_id' => 'required|max:60',
            'reason' => 'string',
            'expires_at' => 'integer',
            'is_global_ban' => 'required|boolean',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $bannedPlayerId = $request->get('player_id');
        $bannedPlayerAlias = $request->get('player_alias');
        $staffPlayerId = $request->get('staff_id');
        $reason = $request->get('reason');
        $expiresAt = $request->get('expires_at');
        $isGlobalBan = $request->get('is_global_ban', false);

        $ban = $this->playerBanService->ban(
            $serverKey,
            $serverKey->server_id,
            $bannedPlayerId,
            $bannedPlayerAlias,
            $staffPlayerId,
            $reason,
            $expiresAt,
            $isGlobalBan
        );

        return new GameBanV1Resource($ban);
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
            'player_id_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'player_id' => 'required',
            'staff_id_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'staff_id' => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $bannedPlayerId = $request->get('player_id');
        $staffPlayerId = $request->get('staff_id');

        $unban = $this->playerBanService->unban(
            $bannedPlayerId,
            $staffPlayerId,
        );

        return new GameUnbanV1Resource($unban);
    }

    public function getPlayerStatus(Request $request)
    {
        $serverKey = $this->getServerKeyFromHeader($request);

        $this->validateRequest($request->all(), [
            'player_id_type' => ['required', Rule::in(PlayerIdentifierType::values())],
            'player_id' => 'required',
        ], [
            'in' => 'Invalid :attribute given. Must be ['.PlayerIdentifierType::allJoined().']',
        ]);

        $bannedPlayerId = $request->get('player_id');

        $activeBan = $this->playerBanLookupService->getStatus($bannedPlayerId);

        if ($activeBan === null) {
            return [
                'data' => null,
            ];
        }

        return new GameBanV1Resource($activeBan);
    }

    private function getServerKeyFromHeader(Request $request): ServerKey
    {
        $authHeader = $request->header('Authorization');

        return $this->serverKeyAuthService->getServerKey($authHeader);
    }
}
