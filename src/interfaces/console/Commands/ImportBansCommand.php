<?php

namespace Interfaces\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Domains\Modules\Players\Models\MinecraftPlayer;
use Carbon\Carbon;
use Domains\Modules\Bans\Models\GameBan;
use Domains\Modules\Bans\Models\GameBanLog;
use Domains\Modules\Bans\Models\GameUnban;
use Domains\Modules\Servers\Models\Server;
use Domains\Modules\GamePlayerType;
use Domains\Modules\Warnings\Models\GameWarning;

class ImportBansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:bans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates PCBridge data to PCB format';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('[Ban data importer]');
        $this->warn('Warning: No check for existence is made before importing bans! This should only be run once in production');

        $this->info('Truncating new ban tables...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        GameBanLog::query()->truncate();
        GameUnban::query()->truncate();
        GameBan::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Importing bans...');
        $bans = DB::connection('mysql_pcbridge')
            ->table('ban_records_bans')
            ->select('*')
            ->get();

        $server = Server::first();

        $bar = $this->output->createProgressBar($bans->count());
        $banMap = [];

        foreach ($bans as $ban) {
            DB::beginTransaction();
            try {
                $oldBanPlayer = DB::connection('mysql_pcbridge')
                    ->table('ban_players')
                    ->where('id', $ban->player_id)
                    ->first();

                $bannedPlayer = MinecraftPlayer::where('uuid', $oldBanPlayer->uuid)->first();
                if ($bannedPlayer === null) {
                    $bannedPlayer = MinecraftPlayer::create([
                        'uuid' => $oldBanPlayer->uuid,
                        'account_id' => null,
                        'playtime' => 0,
                        'last_seen_at' => Carbon::createFromTimestamp($ban->timestamp),
                    ]);
                }

                $oldStaffPlayer = DB::connection('mysql_pcbridge')
                    ->table('ban_players')
                    ->where('id', $ban->staff_id)
                    ->first();

                 if ($oldStaffPlayer->uuid !== 'CONSOLE') {
                    $staffPlayer = MinecraftPlayer::where('uuid', $oldStaffPlayer->uuid)->first();
                    if ($staffPlayer === null) {
                        $staffPlayer = MinecraftPlayer::create([
                            'uuid' => $oldStaffPlayer->uuid,
                            'account_id' => null,
                            'playtime' => 0,
                            'last_seen_at' => Carbon::createFromTimestamp($unban->timestamp),
                        ]);
                    }
                } else {
                    $staffPlayer = null;
                }

                $isActive = $ban->is_banned;
                if ($ban->unban_on !== null) {
                    $expiry =$ban->unban_on;
                    if (time() >= $expiry) {
                        $isActive = false;
                    }
                }

                $newBan = GameBan::create([
                    'server_id' => $server->getKey(),
                    'banned_player_id' => $bannedPlayer->getKey(),
                    'banned_player_type' => GamePlayerType::Minecraft,
                    'banned_alias_at_time' => $oldBanPlayer->alias,
                    'staff_player_id' => $staffPlayer !== null ? $staffPlayer->getKey() : null,
                    'staff_player_type' => GamePlayerType::Minecraft,
                    'reason' => $ban->reason,
                    'is_active' => $isActive,
                    'is_global_ban' => true,
                    'expires_at' => $ban->unban_on !== null ? Carbon::createFromTimestamp($ban->unban_on) : null,
                    'created_at' => Carbon::createFromTimestamp($ban->timestamp),
                    'updated_at' => Carbon::createFromTimestamp($ban->timestamp),
                ]);

                $banMap[$ban->id] = $newBan->getKey();
                
                DB::commit();

            } catch(\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            

            $bar->advance();
        }

        $this->info('Importing unbans...');
        $unbans = DB::connection('mysql_pcbridge')
            ->table('ban_records_unbans')
            ->select('*')
            ->get();

        $bar = $this->output->createProgressBar($unbans->count());

        foreach ($unbans as $unban) {
            DB::beginTransaction();
            try {
                $newBanId = $banMap[$unban->ban_id];
                $ban = GameBan::find($newBanId);

                $oldStaffPlayer = DB::connection('mysql_pcbridge')
                    ->table('ban_players')
                    ->where('id', $unban->staff_id)
                    ->first();

                if ($oldStaffPlayer->uuid !== 'CONSOLE') {
                    $staffPlayer = MinecraftPlayer::where('uuid', $oldStaffPlayer->uuid)->first();
                    if ($staffPlayer === null) {
                        $staffPlayer = MinecraftPlayer::create([
                            'uuid' => $oldStaffPlayer->uuid,
                            'account_id' => null,
                            'playtime' => 0,
                            'last_seen_at' => Carbon::createFromTimestamp($unban->timestamp),
                        ]);
                    }
                } else {
                    $staffPlayer = null;
                }

                GameUnban::create([
                    'game_ban_id' => $newBanId,
                    'staff_player_id' => $staffPlayer !== null ? $staffPlayer->getKey() : null,
                    'staff_player_type' => GamePlayerType::Minecraft,
                    'created_at' => Carbon::createFromTimestamp($unban->timestamp),
                    'updated_at' => Carbon::createFromTimestamp($unban->timestamp),
                ]);

                if ($ban->is_active === true) {
                    $ban->is_active = false;
                    $ban->save();
                }

                DB::commit();

            } catch(\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            

            $bar->advance();
        }

        $this->info('Importing warnings...');
        $warnings = DB::connection('mysql_pcbridge')
            ->table('warnings')
            ->select('*')
            ->get();

        $bar = $this->output->createProgressBar($warnings->count());

        foreach ($warnings as $warning) {
            DB::beginTransaction();
            try {
                $oldBanPlayer = DB::connection('mysql_pcbridge')
                    ->table('ban_players')
                    ->where('id', $warning->player_id)
                    ->first();

                $warnedPlayer = MinecraftPlayer::where('uuid', $oldBanPlayer->uuid)->first();
                if ($warnedPlayer === null) {
                    $warnedPlayer = MinecraftPlayer::create([
                        'uuid' => $oldBanPlayer->uuid,
                        'account_id' => null,
                        'playtime' => 0,
                        'last_seen_at' => Carbon::createFromTimestamp($ban->timestamp),
                    ]);
                }

                $oldStaffPlayer = DB::connection('mysql_pcbridge')
                    ->table('ban_players')
                    ->where('id', $warning->staff_id)
                    ->first();

                $staffPlayer = MinecraftPlayer::where('uuid', $oldStaffPlayer->uuid)->first();
                if ($staffPlayer === null) {
                    $staffPlayer = MinecraftPlayer::create([
                        'uuid' => $oldStaffPlayer->uuid,
                        'account_id' => null,
                        'playtime' => 0,
                        'last_seen_at' => Carbon::createFromTimestamp($unban->timestamp),
                    ]);
                }

               GameWarning::create([
                    'server_id' => $server->getKey(),
                    'warned_player_id' => $warnedPlayer->getKey(),
                    'warned_player_type' => GamePlayerType::Minecraft,
                    'staff_player_id' => $staffPlayer->getKey(),
                    'staff_player_type' => GamePlayerType::Minecraft,
                    'reason' => $warning->reason,
                    'weight' => 1,
                    'is_active' => true,
                    'created_at' => Carbon::createFromTimestamp($warning->timestamp),
                    'updated_at' => Carbon::createFromTimestamp($warning->timestamp),
               ]);

                DB::commit();

            } catch(\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            

            $bar->advance();
        }


        $this->info('Import complete');
    }
}
