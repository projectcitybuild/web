<?php

use App\Domains\PlayerMail\Data\MailCategory;
use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Domains\PlayerMail\Jobs\DistributePlayerMailJob;
use App\Domains\Players\Events\PlayerCreatedEvent;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMail;
use App\Models\PlayerMailBroadcast;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

it('distributes announcements in resumable batches without duplicating recipients', function () {
    Event::fake([PlayerCreatedEvent::class]);
    $players = MinecraftPlayer::factory()->count(501)->create();
    $latePlayer = MinecraftPlayer::factory()->create();
    $mail = PlayerMail::factory()->create([
        'sender_player_id' => null,
        'sender_name' => 'Project City Build',
        'category' => MailCategory::ANNOUNCEMENT,
    ]);
    $broadcast = PlayerMailBroadcast::factory()->create([
        'mail_id' => $mail->id,
        'max_player_id' => $players->last()->id,
    ]);
    Queue::fake([DistributePlayerMailJob::class]);

    (new DistributePlayerMailJob($broadcast->id, 0))->handle();
    (new DistributePlayerMailJob($broadcast->id, 0))->handle();

    expect($broadcast->fresh()->recipients_count)->toBe(500);
    expect($broadcast->fresh()->last_player_id)->toBe($players[499]->id);
    $this->assertDatabaseCount('player_mail_recipients', 500);
    $this->assertDatabaseMissing('player_mail_recipients', [
        'mail_id' => $mail->id,
        'player_id' => $latePlayer->id,
    ]);

    (new DistributePlayerMailJob($broadcast->id, $players[499]->id))->handle();

    expect($broadcast->fresh()->recipients_count)->toBe(501);
    expect($broadcast->fresh()->status)->toBe(PlayerMailBroadcastStatus::PROCESSING);

    (new DistributePlayerMailJob($broadcast->id, $players->last()->id))->handle();

    expect($broadcast->fresh()->status)->toBe(PlayerMailBroadcastStatus::COMPLETED);
    expect($broadcast->fresh()->completed_at)->not->toBeNull();
    $this->assertDatabaseCount('player_mail_recipients', 501);

    Queue::assertPushed(DistributePlayerMailJob::class);
});
