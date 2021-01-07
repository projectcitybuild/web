<?php

namespace App\Console\Commands;

use App\Entities\Bans\Models\GameBan;
use App\Entities\Bans\Models\GameUnban;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class DebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testdebug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("DB_HOST=".env('DB_HOST'));
    }
}
