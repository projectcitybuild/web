<?php

namespace App\Domains\PlayerMail\UseCases;

use App\Domains\PlayerMail\Data\MailCategory;
use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Domains\PlayerMail\Jobs\DistributePlayerMailJob;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMail;
use App\Models\PlayerMailBroadcast;
use Illuminate\Support\Facades\DB;

class BroadcastPlayerMail
{
    public function execute(Account $account, string $subject, string $body): PlayerMailBroadcast
    {
        $broadcast = DB::transaction(function () use ($account, $subject, $body): PlayerMailBroadcast {
            $mail = PlayerMail::create([
                'sender_name' => 'Project City Build',
                'created_by_account_id' => $account->id,
                'category' => MailCategory::ANNOUNCEMENT,
                'subject' => $subject,
                'body' => $body,
            ]);

            return PlayerMailBroadcast::create([
                'mail_id' => $mail->id,
                'status' => PlayerMailBroadcastStatus::QUEUED,
                'max_player_id' => (int) MinecraftPlayer::query()->max('id'),
                'last_player_id' => 0,
                'recipients_count' => 0,
            ]);
        });

        DistributePlayerMailJob::dispatch($broadcast->id, $broadcast->last_player_id);

        return $broadcast;
    }
}
