<?php

namespace App\Console\Commands;

use App\Entities\Bans\Models\GameBan;
use App\Entities\Players\Models\MinecraftPlayer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportEssentialsBansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:essentials-bans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import essentials bans from storage directory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
        $essBans = json_decode(Storage::drive()->get('banned-players.json'), true);

        $formattingRegex = '/§./m';

        foreach ($essBans as $essBan) {
            $essBan['uuid'] = str_replace('-', '', $essBan['uuid']);

            // Check if this player is already known
            $player = MinecraftPlayer::where('uuid', $essBan['uuid'])->first();

            if (! $player) {
                $player = MinecraftPlayer::create([
                    'uuid' => $essBan['uuid'],
                    'playtime' => 0,
                    'last_seen_at' => new Carbon($essBan['created']),
                ]);
            }

            $date = new Carbon($essBan['created']);

            GameBan::create([
                'server_id' => 1,
                'banned_player_id' => $player->getKey(),
                'banned_player_type' => 'minecraft_player',
                'banned_alias_at_time' => $essBan['name'],
                'staff_player_id' => null,
                'staff_player_type' => 'minecraft_player',
                'reason' => $essBan['reason'] . ' (imported ban from ' . preg_replace($formattingRegex, '', $essBan['source']) . ')',
                'is_active' => 1,
                'is_global_ban' => 1,
                'expires_at' => null,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
