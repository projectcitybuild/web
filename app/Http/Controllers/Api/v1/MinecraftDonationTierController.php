<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use Domain\Donations\UseCases\GetDonationTiers;
use Entities\Resources\DonationPerkResource;
use Illuminate\Http\Request;

final class MinecraftDonationTierController extends ApiController
{
    public function show(
        Request          $request,
        string           $uuid,
        GetDonationTiers $getDonationTier,
    ) {
        $uuid = str_replace(search: '-', replace: '', subject: $uuid);

        return DonationPerkResource::collection(
            $getDonationTier->execute(uuid: $uuid)
        );
    }
}
