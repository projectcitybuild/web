<?php

namespace Domain\Donations\UseCases;

use App\Exceptions\Http\NotFoundException;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Support\Collection;

final class GetDonationTiersUseCase
{
    /**
     * @throws NotFoundException if player not found or not linked to an account
     */
    public function execute(string $uuid): Collection
    {
        $existingPlayer = MinecraftPlayer::where('uuid', $uuid)
            ->with('account.donationPerks.donationTier')
            ->first();

        if ($existingPlayer === null) {
            throw new NotFoundException('player_not_found', 'Minecraft player not found for given UUID');
        }

        $account = $existingPlayer->account;
        if ($account === null) {
            throw new NotFoundException('player_not_linked', 'Minecraft player not linked to an account');
        }

        $perks = $account->donationPerks
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->unique('donation_tier_id');

        if ($perks === null || count($perks) === 0) {
            return collect(); // No donation perks for this account
        }

        return $perks;
    }
}
