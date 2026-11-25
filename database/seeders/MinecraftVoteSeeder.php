<?php

namespace Database\Seeders;

use App\Models\MinecraftPlayer;
use App\Models\MinecraftVote;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class MinecraftVoteSeeder extends Seeder
{
    public function run(): void
    {
        $serverId = Server::query()->value('id');

        if ($serverId === null) {
            throw new RuntimeException('Create a server before seeding Minecraft votes.');
        }

        $playerIndex = 0;

        MinecraftPlayer::query()
            ->select('id')
            ->chunkById(100, function (Collection $players) use ($serverId, &$playerIndex): void {
                $votes = [];

                foreach ($players as $player) {
                    $profile = $playerIndex++ % 10;
                    $voteCount = match (true) {
                        $profile < 2 => random_int(20, 35),
                        $profile < 5 => random_int(1, 6),
                        default => 0,
                    };
                    $dateRange = $profile < 2 ? 30 : 90;

                    for ($voteIndex = 0; $voteIndex < $voteCount; $voteIndex++) {
                        $votedAt = now()
                            ->subDays(random_int(1, $dateRange))
                            ->setTime(random_int(0, 23), random_int(0, 59), random_int(0, 59));
                        $receivedAt = $votedAt->copy()->addSeconds(random_int(1, 30));

                        $votes[] = [
                            'server_id' => $serverId,
                            'player_id' => $player->id,
                            'event_id' => (string) Str::uuid(),
                            'service_name' => fake()->randomElement([
                                'MinecraftServers',
                                'PlanetMinecraft',
                                'TopG',
                                'Minecraft-MP',
                            ]),
                            'address' => fake()->ipv4(),
                            'source_timestamp' => ($votedAt->getTimestamp() * 1000) + random_int(0, 999),
                            'voted_at' => $votedAt->toDateTimeString(),
                            'additional_data' => null,
                            'created_at' => $receivedAt->toDateTimeString(),
                            'updated_at' => $receivedAt->toDateTimeString(),
                        ];
                    }
                }

                if ($votes !== []) {
                    MinecraftVote::query()->insert($votes);
                }
            });
    }
}
