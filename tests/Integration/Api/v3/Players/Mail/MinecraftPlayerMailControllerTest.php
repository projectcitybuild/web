<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\PlayerMail\Data\MailCategory;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMail;
use App\Models\PlayerMailRecipient;

it('requires a server token to access the inbox', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->getJson("http://api.localhost/v3/players/{$player->uuid}/mail")
        ->assertUnauthorized();
});

it('sends direct mail between players without website accounts', function () {
    $sender = MinecraftPlayer::factory()->create(['account_id' => null]);
    $recipient = MinecraftPlayer::factory()->create(['account_id' => null]);

    $response = $this->withServerToken()
        ->postJson("http://api.localhost/v3/players/{$sender->uuid}/mail", [
            'recipient_uuid' => $recipient->uuid,
            'subject' => 'Hello',
            'body' => 'Are you online?',
            'category' => 'announcement',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('category', 'direct')
        ->assertJsonPath('sender', $sender->alias)
        ->assertJsonPath('subject', 'Hello')
        ->assertJsonPath('body', 'Are you online?');

    $mailId = $response->json('id');

    $this->assertDatabaseHas('player_mails', [
        'id' => $mailId,
        'sender_player_id' => $sender->id,
        'category' => MailCategory::DIRECT->value,
    ]);
    $this->assertDatabaseHas('player_mail_recipients', [
        'mail_id' => $mailId,
        'player_id' => $recipient->id,
    ]);
});

it('rejects invalid direct mail without creating records', function () {
    $sender = MinecraftPlayer::factory()->create();

    $response = $this->withServerToken()
        ->postJson("http://api.localhost/v3/players/{$sender->uuid}/mail", []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['recipient_uuid', 'subject', 'body']);

    $this->assertDatabaseCount('player_mails', 0);
    $this->assertDatabaseCount('player_mail_recipients', 0);
});

it('validates recipient UUID and message length limits', function () {
    $sender = MinecraftPlayer::factory()->create();

    $response = $this->withServerToken()
        ->postJson("http://api.localhost/v3/players/{$sender->uuid}/mail", [
            'recipient_uuid' => 'not-a-uuid',
            'subject' => str_repeat('s', 192),
            'body' => str_repeat('b', 5001),
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['recipient_uuid', 'subject', 'body']);

    $this->assertDatabaseCount('player_mails', 0);
    $this->assertDatabaseCount('player_mail_recipients', 0);
});

it('limits direct mail sends per player across UUID formats', function () {
    $sender = MinecraftPlayer::factory()->create();
    $recipient = MinecraftPlayer::factory()->create();
    $fullUuid = (new MinecraftUUID($sender->uuid))->full();
    $this->withServerToken();

    for ($sendNumber = 0; $sendNumber < 5; $sendNumber++) {
        $uuid = $sendNumber % 2 === 0 ? $fullUuid : $sender->uuid;

        $this->postJson("http://api.localhost/v3/players/{$uuid}/mail", [
            'recipient_uuid' => $recipient->uuid,
            'subject' => 'Hello',
            'body' => 'A message',
        ])->assertCreated();
    }

    $this->postJson("http://api.localhost/v3/players/{$sender->uuid}/mail", [
        'recipient_uuid' => $recipient->uuid,
        'subject' => 'Too many',
        'body' => 'This message should be rejected.',
    ])->assertTooManyRequests();

    $this->assertDatabaseCount('player_mails', 5);
});

it('returns only inbox metadata belonging to the requested player', function () {
    $player = MinecraftPlayer::factory()->create();
    $otherPlayer = MinecraftPlayer::factory()->create();
    $mail = PlayerMail::factory()->create([
        'subject' => 'For me',
        'body' => 'private message body',
    ]);
    $otherMail = PlayerMail::factory()->create([
        'subject' => 'For someone else',
    ]);
    PlayerMailRecipient::factory()->create([
        'mail_id' => $mail->id,
        'player_id' => $player->id,
    ]);
    PlayerMailRecipient::factory()->create([
        'mail_id' => $otherMail->id,
        'player_id' => $otherPlayer->id,
    ]);

    $response = $this->withServerToken()
        ->getJson("http://api.localhost/v3/players/{$player->uuid}/mail");

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $mail->id)
        ->assertJsonPath('data.0.subject', 'For me')
        ->assertJsonMissingPath('data.0.body');
});

it('paginates the inbox with a cursor and filters by category', function () {
    $player = MinecraftPlayer::factory()->create();
    $directMail = PlayerMail::factory()->create([
        'category' => MailCategory::DIRECT,
    ]);
    $announcement = PlayerMail::factory()->create([
        'category' => MailCategory::ANNOUNCEMENT,
    ]);
    PlayerMailRecipient::factory()->create([
        'mail_id' => $directMail->id,
        'player_id' => $player->id,
        'received_at' => '2026-11-15 12:00:00',
    ]);
    PlayerMailRecipient::factory()->create([
        'mail_id' => $announcement->id,
        'player_id' => $player->id,
        'received_at' => '2026-11-15 12:00:00',
    ]);

    $firstPage = $this->withServerToken()
        ->getJson("http://api.localhost/v3/players/{$player->uuid}/mail?per_page=1&category=direct");

    $firstPage
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.category', 'direct')
        ->assertJsonPath('data.0.id', $directMail->id)
        ->assertJsonPath('next_cursor', null);

    $allMail = $this->getJson("http://api.localhost/v3/players/{$player->uuid}/mail?per_page=1");

    $allMail
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $announcement->id)
        ->assertJsonStructure(['next_cursor']);
    expect($allMail->json('next_cursor'))->toBeString();

    $nextPage = $this->getJson('http://api.localhost/v3/players/'.$player->uuid.'/mail?'.http_build_query([
        'per_page' => 1,
        'cursor' => $allMail->json('next_cursor'),
    ]));

    $nextPage
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $directMail->id);
});

it('preserves the first read timestamp and supports marking mail unread', function () {
    $player = MinecraftPlayer::factory()->create();
    $mail = PlayerMail::factory()->create();
    $recipient = PlayerMailRecipient::factory()->create([
        'mail_id' => $mail->id,
        'player_id' => $player->id,
    ]);

    $this->travelTo('2026-11-15 12:00:00');
    $this->withServerToken()
        ->patchJson("http://api.localhost/v3/players/{$player->uuid}/mail/{$mail->id}/read")
        ->assertOk()
        ->assertJsonPath('id', $mail->id);
    $firstReadAt = $recipient->fresh()->read_at;

    $this->travel(1)->minute();
    $this->patchJson("http://api.localhost/v3/players/{$player->uuid}/mail/{$mail->id}/read")
        ->assertOk();
    expect($recipient->fresh()->read_at->toISOString())
        ->toBe($firstReadAt->toISOString());

    $this->patchJson("http://api.localhost/v3/players/{$player->uuid}/mail/{$mail->id}/unread")
        ->assertOk()
        ->assertJsonPath('read_at', null);

    $this->getJson("http://api.localhost/v3/players/{$player->uuid}/mail/unread-count")
        ->assertOk()
        ->assertJsonPath('count', 1);
});

it('does not expose mail owned by another player', function () {
    $player = MinecraftPlayer::factory()->create();
    $owner = MinecraftPlayer::factory()->create();
    $mail = PlayerMail::factory()->create();
    PlayerMailRecipient::factory()->create([
        'mail_id' => $mail->id,
        'player_id' => $owner->id,
    ]);

    $this->withServerToken()
        ->getJson("http://api.localhost/v3/players/{$player->uuid}/mail/{$mail->id}")
        ->assertNotFound();
});

it('deletes mail only from the recipient inbox', function () {
    $player = MinecraftPlayer::factory()->create();
    $mail = PlayerMail::factory()->create();
    $recipient = PlayerMailRecipient::factory()->create([
        'mail_id' => $mail->id,
        'player_id' => $player->id,
    ]);

    $this->withServerToken()
        ->deleteJson("http://api.localhost/v3/players/{$player->uuid}/mail/{$mail->id}")
        ->assertOk();

    $this->assertSoftDeleted($recipient);
    $this->assertModelExists($mail);
    $this->getJson("http://api.localhost/v3/players/{$player->uuid}/mail/unread-count")
        ->assertJsonPath('count', 0);
});
