<?php

namespace App\Http\Data;

use App\Models\ExternalAccount;
use Spatie\LaravelData\Data;

class AdminDiscordAccountData extends Data
{
    public function __construct(
        public string $provider_user_id,
        public ?string $username,
    ) {}

    public static function fromModel(ExternalAccount $externalAccount): self
    {
        return new self(
            provider_user_id: $externalAccount->provider_user_id,
            username: $externalAccount->username,
        );
    }
}
