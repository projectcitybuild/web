<?php

namespace App\Http\Data;

use App\Core\Utilities\Redact;
use App\Models\Account;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminAccountData extends Data
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public bool $activated,
        public bool|int|null $terms_accepted,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $last_login_at,
        public bool $is_totp_enabled,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        public Optional|array|null $roles,
        public Optional|array|null $badges,
        public Optional|array|null $minecraft_account,
        public Optional|array|null $donations,
        public Optional|array|null $email_change_requests,
        public Optional|array|null $activations,
    ) {}

    public static function fromModel(Account $account, bool $canViewEmail): self
    {
        return new self(
            id: $account->id,
            username: $account->username,
            email: $canViewEmail ? $account->email : Redact::email($account->email),
            activated: $account->activated,
            terms_accepted: $account->terms_accepted,
            last_login_at: $account->last_login_at,
            is_totp_enabled: $account->is_totp_enabled,
            created_at: $account->created_at,
            updated_at: $account->updated_at,
            roles: self::loadedRelation($account, 'roles'),
            badges: self::loadedRelation($account, 'badges'),
            minecraft_account: self::loadedRelation($account, 'minecraftAccount'),
            donations: self::loadedRelation($account, 'donations'),
            email_change_requests: self::loadedRelation($account, 'emailChangeRequests'),
            activations: self::loadedRelation($account, 'activations'),
        );
    }

    private static function loadedRelation(Account $account, string $relation): Optional|array|null
    {
        if (! $account->relationLoaded($relation)) {
            return Optional::create();
        }

        return $account->getRelation($relation)?->toArray();
    }
}
