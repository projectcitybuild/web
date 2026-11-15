<?php

namespace App\Domains\PlayerMail\UseCases;

use App\Domains\PlayerMail\Data\MailCategory;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMailRecipient;
use Illuminate\Support\Facades\DB;

class SendPlayerMail
{
    public function execute(
        MinecraftPlayer $sender,
        MinecraftPlayer $recipient,
        string $subject,
        string $body,
    ): PlayerMailRecipient {
        return DB::transaction(function () use ($sender, $recipient, $subject, $body): PlayerMailRecipient {
            $mail = $sender->sentMail()->create([
                'sender_name' => $sender->alias ?? $sender->uuid,
                'category' => MailCategory::DIRECT,
                'subject' => $subject,
                'body' => $body,
            ]);

            return $recipient->mailRecipients()->create([
                'mail_id' => $mail->id,
                'received_at' => $mail->created_at,
            ]);
        });
    }
}
