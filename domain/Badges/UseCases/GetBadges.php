<?php

namespace Domain\Badges\UseCases;

use Entities\Models\Eloquent\Badge;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class GetBadges
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    public function execute(PlayerIdentifier $identifier): array
    {
        $account = $this->playerLookup
            ->find(identifier: $identifier)
            ?->getLinkedAccount();

        if ($account === null) {
            return [];
        }

        $badges = $account->badges;
        $accountAgeInYears = $account->created_at->diffInYears(now());

        if ($accountAgeInYears >= 3) {
            $badge = new Badge();
            $badge->display_name = $accountAgeInYears.' years on PCB';
            $badge->unicode_icon = '⌚';
            $badges->add($badge);
        }

        return $badges->toArray();
    }
}
