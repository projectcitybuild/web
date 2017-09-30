<?php

namespace App\Routes\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Bans\Models\GameBan;
use App\Modules\Bans\Models\GameUnban;
use App\Modules\Users\Models\GameUser;
use App\Modules\Users\Models\UserAlias;
use App\Modules\Users\Models\UserAliasType;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Servers\Repositories\ServerRepository;
use DB;
use Carbon\Carbon;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:db {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports db data from the current (soon to be old) live website';

    private $aliasRepository;
    private $serverRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserAliasRepository $aliasRepository, ServerRepository $serverRepository)
    {
        parent::__construct();

        $this->aliasRepository = $aliasRepository;
        $this->serverRepository = $serverRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $module = $this->argument('module');

        if($module === 'bans') {
            $this->importBans();
        }
    }

    private function importBans() {
        $this->info('[Ban data importer]');
        $this->warn('Warning: No check for existence is made before importing bans! This should only be run once in production');

        $this->info('Importing game players...');
        $players = DB::connection('mysql_import_pcbridge')
            ->table('ban_players')
            ->select('id', 'alias', 'uuid')
            ->get();

        $uuidType = $this->aliasRepository->getAliasType('MINECRAFT_UUID')->user_alias_type_id;
        $nameType = $this->aliasRepository->getAliasType('MINECRAFT_NAME')->user_alias_type_id;

        $playerBar = $this->output->createProgressBar(count($players));
        $playerList = [];

        DB::beginTransaction();
        try {
            foreach($players as $player) {
                if($player->alias === 'CONSOLE' || $player->uuid === 'CONSOLE') {
                    $playerBar->advance();
                    continue;
                }

                $userAlias = $this->aliasRepository->getAlias($uuidType, $player->uuid);
                if(is_null($userAlias)) {
                    $gameUser = GameUser::create([
                        'user_id' => null,
                    ]);

                    $userAlias = UserAlias::create([
                        'user_alias_type_id' => $uuidType,
                        'alias' => $player->uuid,
                        'game_user_id' => $gameUser->game_user_id,
                    ]);

                    UserAlias::create([
                        'user_alias_type_id' => $nameType,
                        'alias' => $player->alias,
                        'game_user_id' => $gameUser->game_user_id,
                    ]);
                }
                $playerList[$player->id] = $userAlias->game_user_id;
                
                $playerBar->advance();
            }
            DB::commit();

        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        


        $this->info('Importing ban records...');

        $bans = DB::connection('mysql_import_pcbridge')
            ->table('ban_records_bans')
            ->select('*')
            ->get();

        $bar = $this->output->createProgressBar(count($bans));

        $minecraftServer = $this->serverRepository->getServerByName('Survival / Creative [24/7]');
        $banIds = [];
        
        DB::beginTransaction();
        try {
            foreach($bans as $oldBan) {
                $newBan = GameBan::create([
                    'server_id' => $minecraftServer->server_id,
                    'player_game_user_id' => $playerList[$oldBan->player_id],
                    'staff_game_user_id' => $oldBan->staff_id == 2 || $oldBan->staff_id == 4956 ? null : $playerList[$oldBan->staff_id],
                    'reason' => $oldBan->reason,
                    'is_active' => $oldBan->is_banned,
                    'is_global_ban' => true,
                    'expires_at' => $oldBan->unban_on ? Carbon::createFromTimestamp($oldBan->unban_on) : null,
                    'created_at' => Carbon::createFromTimestamp($oldBan->timestamp),
                    'updated_at' => Carbon::createFromTimestamp($oldBan->timestamp),
                ]);
                $banIds[$oldBan->id] = $newBan->game_ban_id;
                
                $bar->advance();
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        $this->info('Importing unban records...');

        $unbans = DB::connection('mysql_import_pcbridge')
            ->table('ban_records_unbans')
            ->select('*')
            ->get();

        $bar = $this->output->createProgressBar(count($unbans));

        DB::beginTransaction();
        try {
            foreach($unbans as $oldUnban) {
                GameUnban::create([
                    'game_ban_id' => $banIds[$oldUnban->ban_id],
                    'staff_game_user_id' => $oldUnban->staff_id == 2 || $oldUnban->staff_id == 4956 ? null : $playerList[$oldUnban->staff_id],
                    'created_at' => Carbon::createFromTimestamp($oldUnban->timestamp),
                ]);

                $ban = GameBan::find($banIds[$oldUnban->ban_id]);
                $ban->updated_at = Carbon::createFromTimestamp($oldUnban->timestamp);
                $ban->save();

                $bar->advance();
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
