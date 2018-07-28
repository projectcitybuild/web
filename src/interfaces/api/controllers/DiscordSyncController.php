<?php

namespace Interfaces\Api\Controllers;

use Interfaces\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as Validator;
use Domains\Modules\Accounts\Repositories\AccountLinkRepository;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use Application\Exceptions\BadRequestException;
use Domains\Modules\Accounts\Resources\AccountResource;

class DiscordSyncController extends ApiController
{

    /**
     * @var AccountLinkRepository
     */
    private $accountLinkRepository;

    /**
     * @var DiscourseUserApi
     */
    private $discourseUserApi;


    public function __construct(AccountLinkRepository $accountLinkRepository,
                                DiscourseUserApi $discourseUserApi) 
    {
        $this->accountLinkRepository = $accountLinkRepository;
        $this->discourseUserApi = $discourseUserApi;
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
                'data' => [
                    'account' => null,
                ],
            ];
        }

        $account = $discordLink->account;

        $json = $this->discourseUserApi->fetchUserByPcbId($account->getKey());
        $user = $json['user'];

        return [
            'data' => [
                'pcb' => new AccountResource($account),
                'discourse' => $user,
            ],
        ];
    }
}
