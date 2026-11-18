<?php

namespace App\Http\Data;

use App\Domains\PlayerMail\Data\MailCategory;
use App\Models\PlayerMailRecipient;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class PlayerMailApiData extends Data
{
    public function __construct(
        public int $id,
        public MailCategory $category,
        public string $sender,
        public string $subject,
        public string $body,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $read_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $received_at,
    ) {}

    public static function fromRecipient(PlayerMailRecipient $recipient): self
    {
        return new self(
            id: $recipient->mail->id,
            category: $recipient->mail->category,
            sender: $recipient->mail->sender_name,
            subject: $recipient->mail->subject,
            body: $recipient->mail->body,
            read_at: $recipient->read_at,
            received_at: $recipient->received_at,
        );
    }
}
