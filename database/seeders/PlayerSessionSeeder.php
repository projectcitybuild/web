<?php

namespace Database\Seeders;

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use Illuminate\Database\Seeder;

class PlayerSessionSeeder extends Seeder
{
    public function run(): void
    {
        MinecraftPlayer::query()
            ->select('id')
            ->chunkById(100, function ($players): void {
                $sessions = [];

                foreach ($players as $player) {
                    $sessionCount = fake()->boolean()
                        ? random_int(2, 5)
                        : random_int(20, 28);
                    $daysAgo = range(1, 28);
                    shuffle($daysAgo);

                    foreach (array_slice($daysAgo, 0, $sessionCount) as $dayOffset) {
                        $startsAt = now()
                            ->startOfDay()
                            ->subDays($dayOffset)
                            ->addHours(random_int(0, 19))
                            ->addMinutes(random_int(0, 59));
                        $seconds = random_int(60, 14_400);

                        $sessions[] = [
                            'player_id' => $player->id,
                            'seconds' => $seconds,
                            'starts_at' => $startsAt->toDateTimeString(),
                            'ends_at' => $startsAt->copy()->addSeconds($seconds)->toDateTimeString(),
                        ];
                    }
                }

                if ($sessions !== []) {
                    MinecraftPlayerSession::query()->insert($sessions);
                }
            });
    }
}
