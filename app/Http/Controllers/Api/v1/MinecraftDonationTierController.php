<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Resources\DonationPerkResource;
use App\Exceptions\Http\NotFoundException;
use App\Http\ApiController;
use Illuminate\Http\Request;

final class MinecraftDonationTierController extends ApiController
{
    public function show(Request $request, string $uuid)
    {
        $uuid = str_replace('-', '', $uuid);

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
            return ['data' => null]; // No donation perks for this account
        }

        return DonationPerkResource::collection($perks);
    }
}
