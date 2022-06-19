<?php

namespace Domain\BanAppeals\UseCases;

use Domain\BanAppeals\Exceptions\EmailRequiredException;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Notifications\BanAppealConfirmationNotification;
use Illuminate\Support\Facades\Http;
use Repositories\BanAppealRepository;

final class CreateBanAppealUseCase
{
    public function __construct(
        private BanAppealRepository $banAppealRepository
    ) {}

    /**
     * Returns whether an account owns the player associated with a ban
     *
     * @param GameBan $ban
     * @param Account|null $account
     * @return bool
     */
    public function isAccountVerified(GameBan $ban, ?Account $account): bool {
        return ($account?->is($ban->bannedPlayer->account)) ?? false;
    }

    /**
     * @throws EmailRequiredException
     */
    public function execute(
        GameBan $ban,
        string $explanation,
        ?Account $loggedInAccount,
        ?string $email
    ): BanAppeal
    {
        $isAccountVerified = $this->isAccountVerified($ban, $loggedInAccount);
        if (!$isAccountVerified && $email === null) {
            throw new EmailRequiredException();
        }

        $banAppeal = $this->banAppealRepository->create(
            gameBanId: $ban->getKey(),
            isAccountVerified: $isAccountVerified,
            explanation: $explanation,
            email: $email
        );

        $banAppeal->notify(new BanAppealConfirmationNotification($banAppeal->showLink()));
        $this->sendDiscordNotification($banAppeal);

        return $banAppeal;
    }


    /**
     * TODO: convert to actual notifications along with builder applications
     */
    private function sendDiscordNotification(BanAppeal $banAppeal): void
    {
        $webhook = config('discord.webhook_ban_appeal_channel');
        if (! empty($webhook)) {
            Http::post($webhook, [
                'content' => "A new ban appeal has been submitted",
                'embeds' => [
                    [
                        "title" => "Ban Appeal",
                        "url" => route('front.panel.ban-appeals.show', $banAppeal->getKey()),
                        "color" => "7506394",
                        "fields" => [
                            [
                                "name" => "Banning Staff",
                                "value" => $banAppeal->additional_notes ?? "-",
                            ],
                            [
                                "name" => "Ban Reason",
                                "value" => $banAppeal->gameBan->reason
                            ],
                            [
                                "name" => "Appeal Reason",
                                "value" => $banAppeal->explanation
                            ]
                        ],
                        "author" => [
                            "name" => $banAppeal->gameBan->bannedPlayer->getBanReadableName() ?? 'No Alias',
                        ]
                    ]
                ],
            ]);
        }
    }
}
