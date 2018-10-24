<?php

namespace Interfaces\Api\Controllers;

use Interfaces\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as Validator;
use Entities\Accounts\Repositories\AccountLinkRepository;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use Application\Exceptions\BadRequestException;
use Entities\Accounts\Resources\AccountResource;

class DiscordSyncController extends ApiController
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
