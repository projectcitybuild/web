<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use Illuminate\Http\Request;
use App\Entities\Players\Repositories\MinecraftAuthCodeRepository;
use App\Entities\Players\Repositories\MinecraftPlayerRepository;
use Carbon\Carbon;
use App\Exceptions\Http\ForbiddenException;
use Illuminate\Support\Str;
use App\Exceptions\Http\UnauthorisedException;
use App\Entities\Groups\Repositories\GroupRepository;
use App\Entities\Accounts\Resources\AccountResource;

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

    /**
     * @var GroupRepository
     */
    private $groupRepository;


    public function __construct(
        MinecraftAuthCodeRepository $minecraftAuthCodeRepository, 
        MinecraftPlayerRepository $minecraftPlayerRepository,
        GroupRepository $groupRepository
    ) {
        $this->minecraftAuthCodeRepository = $minecraftAuthCodeRepository;
        $this->minecraftPlayerRepository = $minecraftPlayerRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Requests an URL that the user can click to link their PCB account
     *
     * @param Request $request
     * @return void
     */
    public function requestTokenUrl(Request $request)
    {
        $this->validateRequest($request->all(), [
            'minecraft_uuid' => 'bail|required|string', // TODO: override UUID rule to allow UUIDs without hyphens
        ]);

        $uuid = $request->get('minecraft_uuid');

        $existingPlayer = $this->minecraftPlayerRepository->getByUuid($uuid);
        if ($existingPlayer === null) 
        {
            $existingPlayer = $this->minecraftPlayerRepository->store($uuid, Carbon::now());
        }

        // we don't currently support re-authenticating a Minecraft account when 
        // it's already authenticated, as this might cause some unexpected results
        if ($existingPlayer->account_id !== null) 
        {
            throw new ForbiddenException('already_authenticated', 'This UUID has already been authenticated');
        }

        // invalidate any existing auth codes since only 1 should be alive
        $this->minecraftAuthCodeRepository->deleteByMinecraftPlayerId($existingPlayer->getKey());

        $authCode = $this->minecraftAuthCodeRepository->store(
            $existingPlayer->getKey(),
            $uuid,
            Str::uuid()->toString(),
            Carbon::now()->addMinutes(30)
        );

        return [
            'data' => [
                'url' => route('front.auth.minecraft.token', ['token' => $authCode->token]),
            ],
        ];
    }

    /**
     * Returns the PCB groups that the given UUID belongs to
     *
     * @param Request $request
     * @return void
     */
    public function getGroupsForUUID(Request $request)
    {
        $this->validateRequest($request->all(), [
            'minecraft_uuid' => 'bail|required|string', // TODO: override UUID rule to allow UUIDs without hyphens
        ]);

        $uuid = $request->get('minecraft_uuid');

        $existingPlayer = $this->minecraftPlayerRepository->getByUuid($uuid);
        if ($existingPlayer === null || $existingPlayer->account === null) 
        {
            throw new UnauthorisedException('account_not_linked', 'This UUID has not been linked to a PCB account. Please complete the authorization flow first');
        }

        // force load groups
        $existingPlayer->account->groups;
        
        return [
            'data' => new AccountResource($existingPlayer->account),
        ];
    }
}
