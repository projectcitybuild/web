<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Donations\UseCases\GetDonationTiers;
use App\Http\Controllers\ApiController;
use App\Http\Resources\DonationPerkResource;
use Illuminate\Http\Request;

final class MinecraftDonationTierController extends ApiController
{
    public function show(
        Request $request,
        MinecraftUUID $uuid,
        GetDonationTiers $getDonationTier,
    ) {
        return DonationPerkResource::collection(
            $getDonationTier->execute(uuid: $uuid)
        );
    }
}
