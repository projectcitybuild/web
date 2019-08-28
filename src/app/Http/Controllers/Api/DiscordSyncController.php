<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as Validator;
use App\Entities\Eloquent\Accounts\Repositories\AccountLinkRepository;
use App\Exceptions\Http\BadRequestException;
use App\Entities\Eloquent\Accounts\Resources\AccountResource;

final class DiscordSyncController extends ApiController
{
    /**
     * @var AccountLinkRepository
     */
    private $accountLinkRepository;

    public function __construct(AccountLinkRepository $accountLinkRepository) 
    {
        $this->accountLinkRepository = $accountLinkRepository;
    }

    public function getRank(Request $request, Validator $validator)
    {
        $validator = $validator->make($request->all(), [
            'discord_id' => 'required',
        ]);

        if ($validator->failed()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }

        $discordUserId = $request->get('discord_id');
        
        $discordLink = $this->accountLinkRepository->getByProviderAccount('discord', $discordUserId);
        if ($discordLink === null) {
            return [
                'data' => null,
            ];
        }

        $account = $discordLink->account;
        $group = $account->groups;

        return [
            'data' => new AccountResource($account),
        ];
    }
}
