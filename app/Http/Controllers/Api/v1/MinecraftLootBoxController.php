<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Donations\Models\MinecraftRedeemedLootBox;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\ApiController;
use App\Http\Controllers\Api\v1\Resources\DonationPerkResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class MinecraftLootBoxController extends ApiController
{
    public function showAvailableBoxes(Request $request, string $uuid)
    {
        $uuid = str_replace('-', '', $uuid);

        $existingPlayer = MinecraftPlayer::where('uuid', $uuid)
            ->with('account.donationPerks.donationTier')
            ->first();

        if ($existingPlayer === null) {
            return ['data' => null]; // Player has never been on our server or never synced
        }

        $account = $existingPlayer->account;
        if ($account === null) {
            return ['data' => null]; // No account linked to Minecraft player
        }

        $perks = $account->donationPerks
            ->where('is_active', true)
            ->where('expires_at', '<', now())
            ->unique('donation_tier_id');

        if ($perks === null || count($perks) === 0) {
            return ['data' => null]; // No donation perks for this account
        }

        $perksWithRedeemedBoxes = MinecraftRedeemedLootBox::where('account_id', $account->getKey())
            ->whereIn('donation_perks_id', $perks->pluck('donation_perks_id')->toArray())
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->pluck('donation_perks_id')
            ->toArray();

        $perksWithUnredeemedBoxes = $perks
            ->filter(fn ($perk, $_) => ! in_array($perk->donation_perks_id, $perksWithRedeemedBoxes));

        if (count($perksWithUnredeemedBoxes) === 0) {
            return [
                'data' => [
                    'seconds_until_redeemable' => Carbon::tomorrow()->diffInSeconds(),
                ],
            ];
        }

        return DonationPerkResource::collection($perksWithUnredeemedBoxes);
    }

    public function redeemBoxes(Request $request, $uuid)
    {
        $uuid = str_replace('-', '', $uuid);

        $existingPlayer = MinecraftPlayer::where('uuid', $uuid)
            ->with('account.donationPerks.donationTier')
            ->first();

        if ($existingPlayer === null) {
            return ['data' => null]; // Player has never been on our server or never synced
        }

        $account = $existingPlayer->account;
        if ($account === null) {
            return ['data' => null]; // No account linked to Minecraft player
        }

        $perks = $account->donationPerks
            ->where('is_active', true)
            ->where('expires_at', '<', now())
            ->unique('donation_tier_id');

        if ($perks === null || count($perks) === 0) {
            return ['data' => null]; // No donation perks for this account
        }

        $perksWithRedeemedBoxes = MinecraftRedeemedLootBox::where('account_id', $account->getKey())
            ->whereIn('donation_perks_id', $perks->pluck('donation_perks_id')->toArray())
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->pluck('donation_perks_id')
            ->toArray();

        $perksWithUnredeemedBoxes = $perks
            ->filter(fn ($perk, $_) => ! in_array($perk->donation_perks_id, $perksWithRedeemedBoxes));

        foreach ($perksWithUnredeemedBoxes as $perk) {
            MinecraftRedeemedLootBox::create([
                'account_id' => $account->getKey(),
                'donation_perks_id' => $perk->getKey(),
                'created_at' => now(),
            ]);
        }

        return [
            'data' => [
                'perks_redeemed' => count($perksWithUnredeemedBoxes),
            ],
        ];
    }
}
