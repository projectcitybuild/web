<?php

use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;

test('homepage shows unique players ordered by their most recent connection', function () {
    $this->travelTo('2026-11-24 12:00:00');

    $newestPlayer = MinecraftPlayer::factory()->create(['alias' => 'NewestPlayer']);
    $middlePlayer = MinecraftPlayer::factory()->create(['alias' => 'MiddlePlayer']);
    $oldestPlayer = MinecraftPlayer::factory()->create(['alias' => 'OldestPlayer']);

    MinecraftPlayerSession::query()->create([
        'player_id' => $newestPlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHour(),
        'ends_at' => now()->subHour()->addMinutes(10),
    ]);
    MinecraftPlayerSession::query()->create([
        'player_id' => $newestPlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHours(8),
        'ends_at' => now()->subHours(8)->addMinutes(10),
    ]);
    MinecraftPlayerSession::query()->create([
        'player_id' => $middlePlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHours(2),
        'ends_at' => now()->subHours(2)->addMinutes(10),
    ]);
    MinecraftPlayerSession::query()->create([
        'player_id' => $oldestPlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHours(6),
        'ends_at' => now()->subHours(6)->addMinutes(10),
    ]);

    $response = $this->get(route('front.home'));

    $response->assertOk()
        ->assertSeeInOrder([
            'NewestPlayer',
            '1 hour ago',
            'MiddlePlayer',
            '2 hours ago',
            'OldestPlayer',
            '6 hours ago',
        ])
        ->assertSee('https://minotar.net/helm/'.$newestPlayer->uuid.'/48', false)
        ->assertSee('https://minotar.net/helm/'.$middlePlayer->uuid.'/48', false)
        ->assertSee('https://minotar.net/helm/'.$oldestPlayer->uuid.'/48', false);

    expect(substr_count($response->getContent(), 'NewestPlayer'))->toBe(1);
});

test('signed-in homepage avatar uses compact dimensions', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->for($account)->create();

    $this->actingAs($account)
        ->get(route('front.home'))
        ->assertOk()
        ->assertSee('width="32" height="32"', false)
        ->assertSee('https://minotar.net/helm/'.$player->uuid, false);
});

test('homepage shows truncated version', function () {
    config([
        'app.version' => 'v123456789',
        'app.commit' => 'abcdef0123456789abcdef0123456789abcdef01',
    ]);

    $this->get(route('front.home'))
        ->assertOk()
        ->assertSee('v123456', false)
        ->assertSee('abcdef0', false)
        ->assertDontSee('v123456789', false);
});

test('homepage does not show unknown version', function () {
    $this->get(route('front.home'))
        ->assertOk()
        ->assertDontSee('unknown', false);
});
