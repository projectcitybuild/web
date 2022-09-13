<?php

namespace App\Console\Commands;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\GameBan;
use Illuminate\Console\Command;

class GenerateScoutIndexesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:scout-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an index for each searchable scout model';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('scout:import', ['model' => Account::class]);
        $this->call('scout:import', ['model' => GameBan::class]);

        return 0;
    }
}
