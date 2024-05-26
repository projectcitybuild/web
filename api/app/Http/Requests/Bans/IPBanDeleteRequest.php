<?php

namespace App\Http\Requests\Bans;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Domains\Bans\Transfers\CreateIPUnbanTransfer;
use Illuminate\Foundation\Http\FormRequest;

class IPBanDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ip' => ['required', 'ip'],
            'unbanner_player_uuid' => ['uuid'],
            'reason' => 'string',
        ];
    }

    public function transfer(): CreateIPUnbanTransfer
    {
        $validated = $this->safe()->collect();

        return new CreateIPUnbanTransfer(
            ip: $validated->get('ip'),
            unbannerPlayerUUID: optional(
                $validated->get('unbanner_player_uuid'),
                fn ($uuid) => new MinecraftUUID($uuid),
            ),
        );
    }
}
