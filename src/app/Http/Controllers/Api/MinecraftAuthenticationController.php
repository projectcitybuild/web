<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use Illuminate\Http\Request;
use App\Entities\Players\Repositories\MinecraftAuthCodeRepository;
use App\Entities\Players\Repositories\MinecraftPlayerRepository;
use Carbon\Carbon;
use App\Exceptions\Http\ForbiddenException;
use Illuminate\Support\Str;

final class MinecraftAuthenticationController extends ApiController
{
    /**
     * @var MinecraftAuthCodeRepository
     */
    private $minecraftAuthCodeRepository;

    /**
     * @var MinecraftPlayerRepository
     */
    private $minecraftPlayerRepository;

    public function __construct(
        MinecraftAuthCodeRepository $minecraftAuthCodeRepository, 
        MinecraftPlayerRepository $minecraftPlayerRepository
    ) {
        $this->minecraftAuthCodeRepository = $minecraftAuthCodeRepository;
        $this->minecraftPlayerRepository = $minecraftPlayerRepository;
    }

    public function requestTokenUrl(Request $request)
    {
        $this->validateRequest($request->all(), [
            'minecraft_uuid' => 'bail|required|string', // TODO: override UUID rule to allow UUIDs without hyphens
        ]);

        $uuid = $request->get('minecraft_uuid');

        $existingPlayer = $this->minecraftPlayerRepository->getByUuid($uuid);
        
        if ($existingPlayer === null) {
            $existingPlayer = $this->minecraftPlayerRepository->store($uuid, Carbon::now());
        }

        // we don't currently support re-authenticating a Minecraft account when 
        // it's already authenticated, as this might cause some unexpected results
        if ($existingPlayer->account_id !== null) {
            throw new ForbiddenException('already_authenticated', 'This UUID has already been authenticated');
        }

        // invalidate any existing auth codes since only 1 should be alive
        $this->minecraftAuthCodeRepository->deleteByMinecraftPlayerId($existingPlayer->getKey());

        $authCode = $this->minecraftAuthCodeRepository->store(
            $existingPlayer->getKey(),
            $uuid,
            Str::uuid(),
            Carbon::now()->addMinutes(30)
        );

        return [
            'data' => [
                'url' => route('front.auth.minecraft.code', ['token' => $authCode->token]),
            ],
        ];
    }
}
