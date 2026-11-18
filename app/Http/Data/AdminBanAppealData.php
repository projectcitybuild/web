<?php

namespace App\Http\Data;

use App\Domains\BanAppeals\Data\BanAppealStatus;
use App\Models\Account;
use App\Models\BanAppeal;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminBanAppealData extends Data
{
    public function __construct(
        public int $id,
        public ?BanAppealStatus $status,
        public ?string $email,
        public ?string $minecraft_uuid,
        public ?string $date_of_ban,
        public ?string $ban_reason,
        public ?string $unban_reason,
        public ?string $decided_at,
        public ?string $decision_note,
        public Optional|AdminAccountData|null $account,
        public Optional|array|null $game_player_ban,
        public Optional|array|null $decider_player,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
    ) {}

    public static function fromModel(BanAppeal $banAppeal, bool $canViewEmail): self
    {
        $account = Optional::create();
        if ($banAppeal->relationLoaded('account')) {
            /** @var Account|null $relatedAccount */
            $relatedAccount = $banAppeal->getRelation('account');
            $account = $relatedAccount === null
                ? null
                : AdminAccountData::fromModel($relatedAccount, $canViewEmail);
        }

        return new self(
            id: $banAppeal->id,
            status: $banAppeal->status,
            email: $banAppeal->email,
            minecraft_uuid: $banAppeal->minecraft_uuid,
            date_of_ban: $banAppeal->date_of_ban,
            ban_reason: $banAppeal->ban_reason,
            unban_reason: $banAppeal->unban_reason,
            decided_at: $banAppeal->decided_at?->toDateTimeString(),
            decision_note: $banAppeal->decision_note,
            account: $account,
            game_player_ban: self::loadedRelation($banAppeal, 'gamePlayerBan'),
            decider_player: self::loadedRelation($banAppeal, 'deciderPlayer'),
            created_at: $banAppeal->created_at,
        );
    }

    private static function loadedRelation(BanAppeal $banAppeal, string $relation): Optional|array|null
    {
        if (! $banAppeal->relationLoaded($relation)) {
            return Optional::create();
        }

        return $banAppeal->getRelation($relation)?->toArray();
    }
}
