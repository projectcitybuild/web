<?php

namespace App\Domains\PlayerMail\UseCases;

use App\Domains\PlayerMail\Data\MailCategory;
use App\Models\MinecraftPlayer;
use Illuminate\Pagination\CursorPaginator;

class GetPlayerMailInbox
{
    public function execute(
        MinecraftPlayer $player,
        int $perPage,
        ?string $cursor,
        ?MailCategory $category,
    ): CursorPaginator {
        return $player->mailRecipients()
            ->with(['mail:id,sender_name,category,subject'])
            ->when(
                $category !== null,
                fn ($query) => $query->whereHas(
                    'mail',
                    fn ($mailQuery) => $mailQuery->where('category', $category->value),
                ),
            )
            ->orderByDesc('received_at')
            ->orderByDesc('mail_id')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }
}
