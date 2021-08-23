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

        $lootBoxes = $account->donationPerks
            ->where('is_active', true)
            ->where('expires_at', '<', now())
            ->unique('donation_tier_id')
            ->filter(fn ($perk) => $perk->donationTier !== null)
            ->flatMap(fn ($perk) => $perk->donationTier->minecraftLootBoxes)
            ->filter(fn ($lootBox) => $lootBox->is_active);

        if ($lootBoxes === null || count($lootBoxes) === 0) {
            return ['data' => []];
        }

        $redeemedBoxes = MinecraftRedeemedLootBox::where('account_id', $account->getKey())
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
        list($unredeemedBoxes, $_) = $this->getUnredeemedBoxesForUUID($uuid);

        if (count($unredeemedBoxes) === 0) {
            return [
                'data' => [
                    'seconds_until_redeemable' => Carbon::tomorrow()->diffInSeconds(),
                ],
            ];
        }
        return MinecraftLootBoxResource::collection($unredeemedBoxes);
    }

    public function redeem(Request $request, $uuid)
    {
        list($unredeemedBoxes, $account) = $this->getUnredeemedBoxesForUUID($uuid);

        foreach ($unredeemedBoxes as $box) {
            MinecraftRedeemedLootBox::create([
                'account_id' => $account->getKey(),
                'minecraft_loot_box_id' => $box->getKey(),
                'created_at' => now(),
            ]);
        }

        if (count($unredeemedBoxes) === 0) {
            return [
                'data' => [
                    'seconds_until_redeemable' => Carbon::tomorrow()->diffInSeconds(),
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
