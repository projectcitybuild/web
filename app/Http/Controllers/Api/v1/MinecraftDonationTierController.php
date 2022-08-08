<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use Domain\Donations\UseCases\GetDonationTiersUseCase;
use Entities\Resources\DonationPerkResource;
use Illuminate\Http\Request;

final class MinecraftDonationTierController extends ApiController
{
    public function show(
        Request                 $request,
        string                  $uuid,
        GetDonationTiersUseCase $getDonationTier,
    ) {
        $uuid = str_replace(search: '-', replace: '', subject: $uuid);

        return DonationPerkResource::collection(
            $getDonationTier->execute(uuid: $uuid)
        );
    }
}
