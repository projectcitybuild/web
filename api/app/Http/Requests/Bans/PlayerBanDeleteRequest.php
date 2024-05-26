<?php

namespace App\Http\Requests\Bans;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Domains\Bans\Transfers\CreatePlayerUnbanTransfer;
use Illuminate\Foundation\Http\FormRequest;

class PlayerBanDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'banned_player_uuid' => ['required', 'uuid'],
            'unbanner_player_uuid' => ['uuid'],
            'reason' => 'string',
        ];
    }

    public function transfer(): CreatePlayerUnbanTransfer
    {
        $validated = $this->safe()->collect();

        return new CreatePlayerUnbanTransfer(
            bannedPlayerUUID: new MinecraftUUID($validated->get('banned_player_uuid')),
            unbannerPlayerUUID: optional(
                $validated->get('unbanner_player_uuid'),
                fn ($uuid) => new MinecraftUUID($uuid),
            ),
            reason: $validated->get('reason'),
        );
    }
}
