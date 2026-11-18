<?php

namespace Tests\Unit\App\Models;

use App\Domains\Bans\Data\UnbanType;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;

it('uses the expiry date when a ban expires', function () {
    $expiryDate = now()->subDay();

    $ban = GamePlayerBan::factory()
        ->bannedBy(MinecraftPlayer::factory())
        ->bannedPlayer(MinecraftPlayer::factory())
        ->create([
            'expires_at' => $expiryDate,
            'unbanned_at' => null,
        ]);

    $this->assertEquals($ban->unbanned_at, $ban->expires_at);
    $this->assertEquals($ban->unban_type, UnbanType::EXPIRED);
});

it('prefers the manually assigned unban date', function () {
    $expiryDate = now()->subDay();
    $unbanDate = now()->subDays(5);

    $ban = GamePlayerBan::factory()
        ->bannedBy(MinecraftPlayer::factory())
        ->bannedPlayer(MinecraftPlayer::factory())
        ->create([
            'expires_at' => $expiryDate,
            'unbanned_at' => $unbanDate,
            'unban_type' => UnbanType::MANUAL->value,
        ]);

    $this->assertEquals($ban->unbanned_at, $unbanDate);
    $this->assertEquals($ban->unban_type, UnbanType::MANUAL);
});
