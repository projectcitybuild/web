<?php

namespace Tests\Unit\App\Models;

use App\Domains\Bans\Data\UnbanType;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GamePlayerBanTest extends TestCase
{
    public function test_unban_date_matches_expiry_date()
    {
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
    }

    public function test_prefers_manually_unbanned_date()
    {
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
    }
}
