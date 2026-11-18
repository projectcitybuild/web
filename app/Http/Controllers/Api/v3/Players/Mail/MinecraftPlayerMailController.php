<?php

namespace App\Http\Controllers\Api\v3\Players\Mail;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\PlayerMail\Data\MailCategory;
use App\Domains\PlayerMail\UseCases\DeletePlayerMail;
use App\Domains\PlayerMail\UseCases\GetPlayerMail;
use App\Domains\PlayerMail\UseCases\GetPlayerMailInbox;
use App\Domains\PlayerMail\UseCases\MarkPlayerMailRead;
use App\Domains\PlayerMail\UseCases\MarkPlayerMailUnread;
use App\Domains\PlayerMail\UseCases\SendPlayerMail;
use App\Http\Controllers\ApiController;
use App\Http\Data\PlayerMailApiData;
use App\Http\Data\PlayerMailInboxData;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMailRecipient;
use Dedoc\Scramble\Attributes\Response as OpenApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MinecraftPlayerMailController extends ApiController
{
    /**
     * @response array{data: list<PlayerMailInboxData>, next_cursor: string|null}
     */
    public function index(
        Request $request,
        MinecraftUUID $minecraftUUID,
        GetPlayerMailInbox $getInbox,
    ): JsonResponse {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'cursor' => ['sometimes', 'nullable', 'string'],
            'category' => ['sometimes', 'string', Rule::in(array_column(MailCategory::cases(), 'value'))],
        ]);
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();
        $category = isset($validated['category'])
            ? MailCategory::from($validated['category'])
            : null;
        $inbox = $getInbox->execute(
            player: $player,
            perPage: (int) ($validated['per_page'] ?? 25),
            cursor: $validated['cursor'] ?? null,
            category: $category,
        );

        return response()->json([
            'data' => array_map(
                fn (PlayerMailRecipient $recipient): array => PlayerMailInboxData::fromRecipient($recipient)->toArray(),
                $inbox->items(),
            ),
            'next_cursor' => $inbox->nextCursor()?->encode(),
        ]);
    }

    public function unreadCount(MinecraftUUID $minecraftUUID): JsonResponse
    {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        return response()->json([
            'count' => $player->mailRecipients()->whereNull('read_at')->count(),
        ]);
    }

    public function show(
        MinecraftUUID $minecraftUUID,
        int $mailId,
        GetPlayerMail $getPlayerMail,
    ): PlayerMailApiData {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        return PlayerMailApiData::fromRecipient($getPlayerMail->execute($player, $mailId));
    }

    #[OpenApiResponse(status: 201, type: PlayerMailApiData::class)]
    public function store(
        Request $request,
        MinecraftUUID $minecraftUUID,
        SendPlayerMail $sendPlayerMail,
    ): JsonResponse {
        $validated = $request->validate([
            'recipient_uuid' => ['required', 'string', new MinecraftUUIDRule],
            'subject' => ['required', 'string', 'max:191'],
            'body' => ['required', 'string', 'max:5000'],
        ]);
        $sender = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();
        $recipientUUID = new MinecraftUUID($validated['recipient_uuid']);
        $recipient = MinecraftPlayer::whereUuid($recipientUUID)->firstOrFail();
        $mailRecipient = $sendPlayerMail->execute(
            sender: $sender,
            recipient: $recipient,
            subject: $validated['subject'],
            body: $validated['body'],
        );
        $mailRecipient->load('mail');

        return response()->json(PlayerMailApiData::fromRecipient($mailRecipient)->toArray(), 201);
    }

    public function markRead(
        MinecraftUUID $minecraftUUID,
        int $mailId,
        MarkPlayerMailRead $markRead,
    ): PlayerMailApiData {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();
        $recipient = $markRead->execute($player, $mailId);

        return PlayerMailApiData::fromRecipient($recipient);
    }

    public function markUnread(
        MinecraftUUID $minecraftUUID,
        int $mailId,
        MarkPlayerMailUnread $markUnread,
    ): PlayerMailApiData {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();
        $recipient = $markUnread->execute($player, $mailId);

        return PlayerMailApiData::fromRecipient($recipient);
    }

    public function destroy(
        MinecraftUUID $minecraftUUID,
        int $mailId,
        DeletePlayerMail $deletePlayerMail,
    ): JsonResponse {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();
        $deletePlayerMail->execute($player, $mailId);

        return response()->json([]);
    }
}
