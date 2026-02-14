<?php

namespace App\Domains\Pim\Services;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Pim\Exceptions\AlreadyElevatedException;
use App\Domains\Pim\Exceptions\NotElevatedException;
use App\Domains\Pim\Notifications\OpGrantedNotification;
use App\Domains\Pim\Notifications\OpRevokedNotification;
use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Notification;

class OpElevationService
{
    public function elevate(MinecraftUUID $playerUuid, string $reason): PlayerOpElevation
    {
        $player = MinecraftPlayer::whereUuid($playerUuid)->first();
        throw_if($player === null, 'Cannot OP elevate null player');

        $existing = PlayerOpElevation::where('player_id', $player->getKey())
            ->whereActive()
            ->first();

        if ($existing !== null) {
            $remaining = now()->diffForHumans($existing->ended_at, [
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'short' => true,
                'parts' => 3,
            ]);
            throw new AlreadyElevatedException('You are already OP elevated ('.$remaining.' remaining)');
        }

        $elevation = PlayerOpElevation::create([
            'player_id' => $player->getKey(),
            'reason' => $reason,
            'started_at' => now(),
            'ended_at' => now()->addHours(3),
        ]);

        $channel = config('discord.webhook_op_elevation_channel', default: '');
        throw_if($channel === '', 'No discord channel set for OP elevation');
        Notification::route('discord', $channel)->notify(
            new OpGrantedNotification($elevation),
        );

        return $elevation;
    }

    public function end(MinecraftUUID $playerUuid): PlayerOpElevation
    {
        $player = MinecraftPlayer::whereUuid($playerUuid)->first();
        throw_if($player === null, 'Cannot OP elevate null player');

        $elevation = PlayerOpElevation::where('player_id', $player->getKey())
            ->whereActive()
            ->first();

        throw_if($elevation === null, NotElevatedException::class);

        $elevation->ended_at = now();
        $elevation->save();

        $channel = config('discord.webhook_op_elevation_channel', default: '');
        throw_if($channel === '', 'No discord channel set for OP elevation');
        Notification::route('discord', $channel)->notify(
            new OpRevokedNotification($elevation),
        );

        return $elevation;
    }
}
