<?php

namespace App\Console\Commands;

use App\Entities\Bans\Models\GameBan;
use App\Entities\Bans\Models\GameUnban;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class StripUUIDHyphensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uuid:repair';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Iterates through every registered Minecraft UUID and strips the hyphens from it. If a stripped version already exists, it combines the records with it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $playersWithHyphenUuids = MinecraftPlayer::where('uuid', 'LIKE', '%-%')->get();

        $progressBar = $this->output->createProgressBar(count($playersWithHyphenUuids));
        $progressBar->start();

        DB::transaction(function () use ($playersWithHyphenUuids, &$progressBar) {
            foreach ($playersWithHyphenUuids as $unstrippedPlayer) {
                // Check if an account exists with the UUID stripped
                $newUuid = str_replace('-', '', $unstrippedPlayer->uuid);
                $originalAccount = MinecraftPlayer::where('uuid', $newUuid)->first();

                // If no duplicate account, update the UUID and move on
                if ($originalAccount === null) {
                    $unstrippedPlayer->uuid = $newUuid;
                    $unstrippedPlayer->save();

                    $progressBar->advance();
                    continue;
                }

                // Otherwise, combine the accounts into the original (stripped) version
                $bans = GameBan::where('banned_player_id', $unstrippedPlayer->getKey())->get();

                foreach ($bans as $ban) {
                    $ban->banned_player_id = $originalAccount->getKey();
                    $ban->save();
                }

                $bans = GameBan::where('staff_player_id', $unstrippedPlayer->getKey())->get();

                foreach ($bans as $ban) {
                    $ban->staff_player_id = $originalAccount->getKey();
                    $ban->save();
                }

                $unbans = GameUnban::where('staff_player_id', $unstrippedPlayer->getKey())->get();

                foreach ($unbans as $unban) {
                    $unban->staff_player_id = $originalAccount->getKey();
                    $unban->save();
                }

                // Transfer Minecraft alias if necessary
                $alias = MinecraftPlayerAlias::where('player_minecraft_id', $unstrippedPlayer->getKey())->first();
                $existingAlias = MinecraftPlayerAlias::where('player_minecraft_id', $originalAccount->getKey())->first();

                if ($alias !== null && $existingAlias !== null) {
                    if ($existingAlias->updated_at < $alias->updated_at) {
                        $existingAlias->alias = $alias->alias;
                        $existingAlias->save();
                    }
                    $alias->delete();
                }

                // Transfer linked account_id if necessary
                if ($unstrippedPlayer->account_id !== null) {
                    $originalAccount->account_id = $unstrippedPlayer->account_id;
                    $originalAccount->save();
                }

                // Delete unstripped UUID player
                $unstrippedPlayer->delete();

                $progressBar->advance();
            }
        });

        $progressBar->finish();
    }
}
