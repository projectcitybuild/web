<?php

namespace App\Http\Requests\Bans;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Domains\Bans\Transfers\CreateIPBanTransfer;
use Illuminate\Foundation\Http\FormRequest;

class IPBanStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ip' => ['required', 'ip'],
            'banner_player_uuid' => 'uuid',
            'reason' => 'string',
        ];
    }

    public function transfer(): CreateIPBanTransfer
    {
        $validated = $this->safe()->collect();

        return new CreateIPBanTransfer(
            ip: $validated->get('ip'),
            bannerPlayerUUID: optional(
                $validated->get('banner_player_uuid'),
                fn ($uuid) => new MinecraftUUID($uuid),
            ),
            reason: $validated->get('reason'),
        );
    }
}
