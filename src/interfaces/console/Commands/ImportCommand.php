<?php

namespace Interfaces\Console\Commands;

use Illuminate\Console\Command;
use Domains\Modules\Bans\Models\GameBan;
use Domains\Modules\Bans\Models\GameUnban;
use Domains\Modules\Servers\Repositories\ServerRepository;
use Domains\Modules\Donations\Models\Donation;
use DB;
use Cache;
use Carbon\Carbon;
use Domains\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangApiService;
use Domains\Modules\Players\Models\MinecraftPlayer;
use Domains\Modules\Players\Models\MinecraftPlayerAlias;
use Domains\Modules\Servers\Models\ServerStatus;
use Domains\Modules\Servers\Models\ServerStatusPlayer;
use Domains\Modules\Players\Services\MinecraftPlayerLookupService;
use Domains\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangPlayer;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Application\Exceptions\TooManyRequestsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Domains\Modules\Accounts\Models\Account;
use Hash;

/**
 * Warning: this code is total spaghetti...
 */
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
    public function __construct(ServerRepository $serverRepository)
    {
        parent::__construct();

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

        switch ($module) {
            case 'bans':
                return $this->importBans();
            // case 'donations':
                // return $this->importDonations();
            // case 'statuses':
                // return $this->importServerStatuses();
            // case 'smf':
            //     return $this->importSmf();
            // case 'users':
                // return $this->importUsers();
            default:
                $this->error('Invalid import module name. Valid: [bans]');
                break;
        }
    }

    private function importBans()
    {
        $this->info('[Ban data importer]');
        $this->warn('Warning: No check for existence is made before importing bans! This should only be run once in production');

        $this->info('Importing game players...');
        $players = DB::connection('mysql_import_pcbridge')
            ->table('ban_players')
            ->select('id', 'alias', 'uuid')
            ->get();

        $playerBar = $this->output->createProgressBar(count($players));
        $playerList = [];

        $playerIdToUuid = [];
        $playerIdToAlias = [];

        DB::beginTransaction();
        try {
            foreach ($players as $player) {
                if ($player->alias === 'CONSOLE' || $player->uuid === 'CONSOLE') {
                    $playerBar->advance();
                    continue;
                }

                $userAlias = $this->aliasRepository->getAlias(UserAliasTypeEnum::MINECRAFT_UUID, $player->uuid);
                if (is_null($userAlias)) {
                    $gameUser = GameUser::create([
                        'user_id' => null,
                    ]);

                    $userAlias = UserAlias::create([
                        'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_UUID,
                        'alias' => $player->uuid,
                        'game_user_id' => $gameUser->game_user_id,
                    ]);

                    UserAlias::create([
                        'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_NAME,
                        'alias' => $player->alias,
                        'game_user_id' => $gameUser->game_user_id,
                    ]);

                    $playerIdToUuid[$userAlias->game_user_id] = $userAlias->user_alias_id;
                    $playerIdToAlias[$userAlias->game_user_id] = $player->alias;
                }
                $playerList[$player->id] = $userAlias->game_user_id;
                
                $playerBar->advance();
            }
            DB::commit();
        } catch (\Exception $e) {
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
            foreach ($bans as $oldBan) {
                $newPlayerId = $playerList[$oldBan->player_id];

                $newBan = GameBan::create([
                    'server_id' => $minecraftServer->server_id,
                    'player_game_user_id' => $newPlayerId,
                    'staff_game_user_id' => $oldBan->staff_id == 2 || $oldBan->staff_id == 4956 ? null : $playerList[$oldBan->staff_id],
                    'banned_alias_id' => $playerIdToUuid[$newPlayerId],
                    'player_alias_at_ban' => $playerIdToAlias[$newPlayerId],
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
        } catch (\Exception $e) {
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
            foreach ($unbans as $oldUnban) {
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
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->info('Import complete');
    }

    private function importDonations()
    {
        // $this->info('[Donation data importer]');
        // $this->warn('Warning: No check for existence is made before importing donations! This should only be run once in production');


        // $uuidFetcher = resolve(MojangApiService::class);

        // $this->info('Fetching donation records...');
        // $donations = DB::connection('mysql_import_pcb')
        //     ->table('donators')
        //     ->select('*')
        //     ->get();

        // $this->info('Importing game players...');
        // $bar = $this->output->createProgressBar(count($donations));

        // DB::beginTransaction();
        // try {
        //     foreach($donations as $donation) {
        //         $username = $donation->username;

        //         if($username === 'Dirtdog101') {
        //             $bar->advance();
        //             continue;
        //         }

        //         $matchingForumUser = Cache::remember('donation_'.$username, 120, function() use($username) {
        //             return DB::connection('mysql_forums')
        //                 ->table('members')
        //                 ->select('id_member', 'real_name', 'member_name', 'email_address')
        //                 ->where('real_name', $username)
        //                 ->orWhere('member_name', $username)
        //                 ->first();
        //         });
    
        //         if($matchingForumUser === null) {
        //             throw new \Exception('Could not find old forum account for ' . $username);
        //         }

        //         $email = $matchingForumUser->email_address;
        //         if($email === null || $email === '') {
        //             throw new \Exception('Empty email address for '. $username);
        //         }

        //         $account = Account::where('email', $email)->first();
        //         if($account === null) {
        //             throw new \Exception('No account for email ' . $email . ' ('.$username.')');
        //         }
                
        //         $expiryDate = Carbon::createFromFormat('Y-m-d', $donation->end_date);
        //         $createDate = Carbon::createFromFormat('Y-m-d', $donation->start_date);

        //         $updateDate = $createDate;
        //         $isActive = true;

        //         $hasExpired = !$donation->lifetime && $expiryDate <= Carbon::now();
        //         if($hasExpired) {
        //             $updateDate = $expiryDate;
        //             $isActive = false;
        //         }
    
        //         Donation::create([
        //             'account_id'        => $account->getKey(),
        //             'amount'            => $donation->amount,
        //             'perks_end_at'      => $donation->lifetime ? null : $expiryDate,
        //             'is_lifetime_perks' => $donation->lifetime,
        //             'is_active'         => $isActive,
        //             'created_at'        => $createDate,
        //             'updated_at'        => $updateDate,
        //         ]);

        //         $bar->advance();
        //     }

        //     DB::commit();
        
        // } catch(\Exception $e) {
        //     DB::rollBack();
        //     $this->error($e->getMessage());
        //     return;
        // }

        // $this->info('Import complete');
    }

    private function importSmf()
    {
        // $this->info('[Smf -> Discourse importer]');
        // $this->warn('Warning: No check for existence is made before importing! This should only be run once in production');

        // $this->info('Preparing cache...');
        // $lastMessageId = Cache::get('importer_smf_pms', -1);
        // $this->info('Last imported id: '. $lastMessageId);

        // $this->info('Preparing http client...');
        // $client = new Client();
        // $key = env('DISCOURSE_API_KEY');
        // if(!$key) {
        //     throw new \Exception('DISCOURSE_API_KEY not set in env');
        // }

        // $this->info('Importing private messages...');
        // $this->info('Fetching messages...');
        // $messages = DB::connection('mysql_forums')
        //     ->table('personal_messages')
        //     ->where('id_pm', '>', $lastMessageId)
        //     ->orderBy('id_pm', 'asc')
        //     ->get();

        // $this->info('Fetching recipients...');
        // $recipients = DB::connection('mysql_forums')
        //     ->table('pm_recipients')
        //     ->where('id_pm', '>', $lastMessageId)
        //     ->get();


        // $fromUserIds = $messages
        //     ->pluck('id_member_from')
        //     ->unique();

        // $toUserIds = $recipients
        //     ->pluck('id_member')
        //     ->unique();

        // $userIds = $fromUserIds->merge($toUserIds)->unique()->toArray();
        // sort($userIds);

        // $this->info('Fetching users...');
        // $users = DB::connection('mysql_forums')
        //     ->table('members')
        //     ->whereIn('id_member', $userIds)
        //     ->get()
        //     ->keyBy('id_member');

        // $this->info('Beginning import...');
        // $apiKeys = [];
        // $progress = $this->output->createProgressBar(count($messages));
        // foreach($messages as $message) {

        //     // manual id skip
        //     $pmIdBlacklist = [2166, 2938, 3404, 3759, 4232, 5074, 5189, 5202, 6090, 6304, 6501, 6521, 6637, 6645, 6661, 7065, 7248, 7408, 7443, 7614];
        //     if(in_array($message->id_pm, $pmIdBlacklist) === true) {
        //         $progress->advance();
        //         continue;
        //     }

        //     if($message->id_member_from === 0) {
        //         $progress->advance();
        //         continue;
        //     }

        //     $toUserIds = $recipients
        //         ->where('id_pm', $message->id_pm)
        //         ->where('deleted', 0)
        //         ->where('id_member', '!=', 0)
        //         ->pluck('id_member');

        //     $fromUser = $users->get($message->id_member_from);

        //     if($fromUser === null) {
        //         throw new \Exception('Could not find FROM user for ['.$message->id_pm.']');
        //     }

        //     // don't bother import if no one has a copy of the pm in their inbox
        //     if(count($toUserIds->toArray()) === 0) {
        //         $progress->advance();
        //         continue;
        //     }

        //     $toUsers = [];
        //     foreach($toUserIds as $id) {
        //         $name = $users->get($id)->member_name;
        //         // skip non-existent users
        //         $nameBlacklist = ['PCB_Econ', 'isonb54321', '_ST0N3_', 'RemusS_'];
        //         if(in_array($name, $nameBlacklist) === true) {
        //             continue;
        //         }
        //         if($name === 'Mango_Bear_') {
        //             $name = 'Mango_Bear';
        //         }
        //         $toUsers[] = $name;
        //     }
        //     $toUsers = implode(',', $toUsers);
            
        //     if(count($toUsers) === 0 || $toUsers === null || $toUsers === "") {
        //         $progress->advance();
        //         continue;
        //     }

        //     try {
        //         while(true) {
        //             try {
        //                 $client->post('https://forums.projectcitybuild.com/posts?api_key='.$key.'&api_username='.$fromUser->member_name, [
        //                     'form_params' => [
        //                         'title'             => $message->subject,
        //                         'raw'               => $message->body,
        //                         'target_usernames'  => $toUsers,
        //                         'archetype'         => 'private_message',
        //                         'created_at'        => Carbon::createFromTimestamp($message->msgtime)->toDateTimeString(),
        //                     ],
        //                 ]);
        //                 break;
    
        //             } catch (ClientException $e) {
        //                 $response = $e->getResponse();
        //                 // skip 403 Forbidden errors since the user was probably banned
        //                 if($response->getStatusCode() === 403) {
        //                     $progress->advance();
        //                     break;
        //                 }
        //                 if($response->getStatusCode() === 429) {
        //                     $this->info('Rate limited. Sleeping for 10 seconds');
        //                     sleep(11);
        //                     $this->info('Resuming...');
        //                     continue;
        //                 }
        //                 throw $e;
        //             }
        //         }

        //     } catch(\Exception $e) {
        //         dump($message);
        //         dump('https://forums.projectcitybuild.com/posts?api_key='.$key.'&api_username='.$fromUser->member_name);
        //         dump('Sender: '. $fromUser->member_name);
        //         dump(['form_params' => [
        //             'title'             => $message->subject,
        //             'raw'               => $message->body,
        //             'target_usernames'  => $toUsers,
        //             'archetype'         => 'private_message',
        //             'created_at'        => Carbon::createFromTimestamp($message->msgtime)->toDateTimeString(),
        //         ]]);

        //         dump($message);
        //         throw $e;
        //     }
           

        //     $lastMessageId = $message->id_pm;
        //     Cache::forever('importer_smf_pms', $lastMessageId);

        //     $progress->advance();
        // }

        // $this->info('Import complete');
    }

    private function importUsers()
    {
        // $this->info('[SMF user importer]');
        // $this->warn('Warning: No check for existence is made before importing! This should only be run once in production');

        // $this->info('Beginning import...');

        // $users = DB::connection('mysql_forums')
        //     ->table('members')
        //     ->select('*')
        //     ->orderBy('id_member', 'asc')
        //     ->chunk(100, function($users) {
        //         DB::transaction(function() use($users) {
        //             $progress = $this->output->createProgressBar(count($users));
                    
        //             foreach($users as $user) {
        //                 Account::create([
        //                     'email'         => $user->email_address,
        //                     'password'      => Hash::make(time()),
        //                     'created_at'    => Carbon::createFromTimestamp($user->date_registered),
        //                     'updated_at'    => Carbon::now(),
        //                 ]);
            
        //                 $progress->advance();
        //             }
        //         });
        //     });

        // $this->info('Import complete');
    }

    private function importServerStatuses()
    {
        //     $this->info('[Server status data importer]');
    //     $this->warn('Warning: No check for existence is made before importing server statuses! This should only be run once in production');

    //     $this->info('Loading cache');
    //     $uuidCache = Cache::get('importer_uuid_cache', []);


    //     $lastStatusId = ServerStatus::orderBy('server_status_id', 'desc')->first();
    //     $lastStatusId = $lastStatusId ? $lastStatusId->server_status_id : 0;

    //     $uuidFetcher = resolve(MojangApiService::class);

    //     $this->info('Creating token bucket...');
    //     $storage    = new FileStorage(__DIR__ . '/mojang.bucket');
    //     $rate       = new Rate(1, Rate::SECOND); // 600 requests per 10 minutes = 1 p/second
    //     $bucket     = new TokenBucket(500, $rate, $storage);
    //     $consumer   = new BlockingConsumer($bucket);
    //     $bucket->bootstrap(500);

    //     $userLookupService = resolve(MinecraftPlayerLookupService::class);


    //     $playerCache = [];

    //     $this->info('Fetching old records...');
    //     $statuses = DB::connection('mysql_import_pcb_statuses')
    //         ->table('pcb_server_status')
    //         ->select('*')
    //         ->where('id', '>', $lastStatusId)
    //         ->orderBy('id', 'asc')
    //         ->chunk(100, function($statuses) use($userLookupService, $uuidFetcher, $consumer, &$uuidCache, &$playerCache) {
    //             foreach($statuses as $status) {

    //                 $isUuidCacheDirty = false;
                    
    //                 $this->info('Beginning import of status id='.$status->id);
    //                 DB::beginTransaction();
    //                 try {
    //                     $newStatus = ServerStatus::create([
    //                         'server_id'         => $status->server_id,
    //                         'is_online'         => $status->is_online,
    //                         'num_of_players'    => $status->current_players,
    //                         'num_of_slots'      => $status->max_players,
    //                         'created_at'        => $status->date,
    //                         'updated_at'        => $status->date,
    //                     ]);
    
    //                     $players = explode(',', $status->players);
    //                     foreach($players as $player) {
    //                         if(empty($player)) {
    //                             continue;
    //                         }

    //                         // use cache where possible
    //                         $uuid = null;
    //                         if(array_key_exists($player, $uuidCache)) {
    //                             $uuid = $uuidCache[$player];
    //                             // if($uuid) {
    //                                 // $this->info('Using cache: uuid='.$uuid->getUuid().' alias='.$uuid->getAlias());
    //                             // }
    //                         }
    
    //                         if($uuid === null) {
    //                             $hasResponse = false;
    //                             while(!$hasResponse) {
    //                                 try {
    //                                     $consumer->consume(1);
    //                                     $timestamp = (new Carbon($status->date))->timestamp;
    //                                     $uuid = $uuidFetcher->getUuidOf($player, $timestamp);
    //                                     if($uuid) {
    //                                         $uuidCache[$player] = $uuid;
    //                                         $isUuidCacheDirty = true;
    //                                         // $this->info('Storing uuid='.$uuid->getUuid().' alias='.$uuid->getAlias());
    //                                     }
    //                                     $hasResponse = true;
    //                                 } catch(TooManyRequestsException $e) {
    //                                     $this->info('Too many requests - resuming in 5 seconds...');
    //                                     sleep(5);
    //                                 }
    //                             }
    //                         }
    //                         if($uuid === null) {
    //                             $hasResponse = false;
    //                             while(!$hasResponse) {
    //                                 try {
    //                                     $consumer->consume(1);
    //                                     $uuid = $uuidFetcher->getOriginalOwnerUuidOf($player);
    //                                     if($uuid) {
    //                                         $uuidCache[$player] = $uuid;
    //                                         $isUuidCacheDirty = true;
    //                                         // $this->info('Storing uuid='.$uuid->getUuid().' alias='.$uuid->getAlias());
    //                                     }
    //                                     $hasResponse = true;
    //                                 } catch(TooManyRequestsException $e) {
    //                                     $this->info('Too many requests - resuming in 5 seconds...');
    //                                     sleep(5);
    //                                 }
    //                             }
    //                         }
    //                         if($uuid === null) {
    //                             $hasResponse = false;
    //                             while(!$hasResponse) {
    //                                 try {
    //                                     $consumer->consume(1);
    //                                     $uuid = $uuidFetcher->getUuidOf($player);
    //                                     if($uuid) {
    //                                         $uuidCache[$player] = $uuid;
    //                                         $isUuidCacheDirty = true;
    //                                         // $this->info('Storing uuid='.$uuid->getUuid().' alias='.$uuid->getAlias());
    //                                     }
    //                                     $hasResponse = true;
    //                                 } catch(TooManyRequestsException $e) {
    //                                     $this->info('Too many requests - resuming in 5 seconds...');
    //                                     sleep(5);
    //                                 }
    //                             }
    //                         }
    //                         if($uuid === null) {
    //                             continue;
    //                             //     throw new \Exception('Could not determine UUID for ' . $player);
    //                         }
    
    //                         if(array_key_exists($uuid->getUuid(), $playerCache)) {
    //                             $playerId = $playerCache[$uuid->getUuid()];
    //                         } else {
    //                             $playerId = $userLookupService->getOrCreateByUuid($uuid->getUuid(), $uuid->getAlias());
    //                             $playerCache[$uuid->getUuid()] = $playerId;
    //                         }

    //                         $minecraftPlayer = ServerStatusPlayer::create([
    //                             'server_status_id' => $newStatus->server_status_id,
    //                             'player_type'      => 'minecraft_player',
    //                             'player_id'        => $playerId->getKey(),
    //                         ]);

    //                         // $this->info('Created Minecraft Player record id='.$minecraftPlayer->getKey());
    //                         if($isUuidCacheDirty) {
    //                             Cache::put('importer_uuid_cache', $uuidCache, 10080);
    //                         }
    //                     }

    //                     DB::commit();
                    
    //                 } catch(\Exception $e) {
    //                     DB::rollBack();
    //                     $this->error('Failed on id='.$status->id.' date='.$status->date);
    //                     throw $e;
    //                 }
                    

    //                 $this->info('Imported id='.$status->id);
    //             }
    //         });
    }
}
