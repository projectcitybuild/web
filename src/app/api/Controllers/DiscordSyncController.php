<?php

namespace App\Api\Controllers;

use App\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as Validator;
use App\Modules\Accounts\Repositories\AccountLinkRepository;
use App\Library\Discourse\Api\DiscourseUserApi;
use App\Support\Exceptions\BadRequestException;
use App\Modules\Accounts\Resources\AccountResource;

class DiscordSyncController extends ApiController {

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


    public function getRank(Request $request, Validator $validator) {
        $validator = $validator->make($request->all(), [
            'discord_id' => 'required',
        ]);

        if($validator->fails()) {
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
