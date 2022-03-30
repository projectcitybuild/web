<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Donations\Models\MinecraftRedeemedLootBox;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Exceptions\Http\NotFoundException;
use App\Http\ApiController;
use App\Http\Controllers\Api\v1\Resources\MinecraftLootBoxResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class MinecraftLootBoxController extends ApiController
{
    private function getUnredeemedBoxesForUUID(string $uuid): array
    {
        $uuid = str_replace('-', '', $uuid);

        $existingPlayer = MinecraftPlayer::where('uuid', $uuid)
            ->with('account.donationPerks.donationTier.minecraftLootBoxes')
            ->first();

        if ($existingPlayer === null) {
            throw new NotFoundException('player_not_found', 'Minecraft player not found for given UUID');
        }

        $account = $existingPlayer->account;
        if ($account === null) {
            throw new NotFoundException('player_not_linked', 'Minecraft player not linked to an account');
        }

        if ($account->donationPerks === null || $account->donationPerks->count() === 0) {
            throw new NotFoundException('no_donor_perks', 'Player does not have any donor perks');
        }

        $lootBoxes = $account->donationPerks
            ->filter(fn ($perk) => $perk->isActive())
            ->unique('donation_tier_id')
            ->filter(fn ($perk) => $perk->donationTier !== null)
            ->flatMap(fn ($perk) => $perk->donationTier->minecraftLootBoxes)
            ->filter(fn ($lootBox) => $lootBox->is_active);

        if ($lootBoxes === null || $lootBoxes->count() === 0) {
            throw new NotFoundException('no_redeemable_boxes', 'Player does not have any perks that allow redeeming boxes');
        }

        $redeemedBoxes = MinecraftRedeemedLootBox::where('account_id', $account->getKey()) // @phpstan-ignore-line
            ->whereIn('minecraft_loot_box_id', $lootBoxes->pluck('minecraft_loot_box_id')->toArray())
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->pluck('minecraft_loot_box_id')
            ->toArray();

        return [
            $lootBoxes->filter(fn ($box) => ! in_array($box->getKey(), $redeemedBoxes)),
            $account,
        ];
    }

    public function showAvailable(Request $request, string $uuid)
    {
        [$unredeemedBoxes, $_] = $this->getUnredeemedBoxesForUUID($uuid);

        if ($unredeemedBoxes->count() === 0) {
            return [
                'data' => [
                    'seconds_until_redeemable' => Carbon::now()->secondsUntilEndOfDay(),
                ],
            ];
        }

        return [
            'data' => [
                'redeemable_boxes' => MinecraftLootBoxResource::collection($unredeemedBoxes),
            ],
        ];
    }

    public function redeem(Request $request, $uuid)
    {
        [$unredeemedBoxes, $account] = $this->getUnredeemedBoxesForUUID($uuid);

        foreach ($unredeemedBoxes as $box) {
            MinecraftRedeemedLootBox::create([
                'account_id' => $account->getKey(),
                'minecraft_loot_box_id' => $box->getKey(),
                'created_at' => now(),
            ]);
        }

        if ($unredeemedBoxes->count() === 0) {
            return [
                'data' => [
                    'seconds_until_redeemable' => Carbon::now()->secondsUntilEndOfDay(),
                ],
            ];
        }

        return [
            'data' => [
                'redeemed_boxes' => MinecraftLootBoxResource::collection($unredeemedBoxes),
            ],
        ];
    }
}
