<?php

namespace App\Core\Domains\Discord;

use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Support\Facades\Http;

class DiscordWebhook
{
    public function send(
        string $url,
        DiscordMessage $message,
        bool $wait,
    ) {
        throw_if(
            empty($message->content) === null && empty($message->embeds),
            code: 400,
            message: 'At least content or embeds must be specified',
        );
        Http::asJson()->post($url, [
            ...$message->toJson(),
            // Whether to ask Discord to actually await the send result, rather than returning immediately
            'wait' => $wait,
        ])
            ->throwIfClientError()
            ->throwIfServerError();
    }
}
