<?php

namespace App\Console\Commands;

use App\Entities\Models\Eloquent\Server;
use App\Entities\Models\Eloquent\ServerKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class ServerKeyCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-key:create {--server_id=} {--can_global_ban=} {--can_warn=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a server key for the given server_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('server_id') === null) {
            $this->error('No `server_id` provided');

            return;
        }

        $serverId = $this->option('server_id');
        $canGlobalBan = $this->option('can_global_ban') ?? false;
        $canWarn = $this->option('can_warn') ?? false;

        $server = Server::find($serverId);
        if ($server === null) {
            $this->error('Server with the given id does not exist');

            return;
        }

        $token = Str::random(32);

        ServerKey::create([
            'server_id' => $serverId,
            'token' => $token,
            'can_local_ban' => true,
            'can_global_ban' => (bool) $canGlobalBan,
            'can_warn' => (bool) $canWarn,
        ]);

        $this->info('Token created: '.$token);
    }
}
