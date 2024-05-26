<?php

namespace App\Http\Requests\Bans;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Domains\Bans\Transfers\CreatePlayerUnbanTransfer;
use Illuminate\Foundation\Http\FormRequest;

class PlayerBanDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banned_player_uuid' => ['required', 'uuid'],
            'unbanner_player_uuid' => ['uuid'],
            'reason' => 'string',
        ];
    }

    public function playerUnban(): CreatePlayerUnbanTransfer
    {
        $validated = $this->safe()->collect();

        return new CreatePlayerUnbanTransfer(
            bannedPlayerUUID: new MinecraftUUID($validated->get('banned_player_uuid')),
            unbannerPlayerUUID: $validated->has('unbanner_player_uuid')
                ? new MinecraftUUID($validated->get('unbanner_player_uuid'))
                : null,
            reason: $validated->get('reason'),
        );
    }
}
