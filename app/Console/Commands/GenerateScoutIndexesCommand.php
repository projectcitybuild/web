<?php

namespace App\Console\Commands;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Illuminate\Console\Command;

class GenerateScoutIndexesCommand extends Command
{
    protected $signature = 'generate:scout-index';
    protected $description = 'Creates an index for each searchable scout model';
    private $models = [
        Account::class,
        GameBan::class,
        MinecraftPlayerAlias::class,
    ];

    public function handle()
    {
        collect($this->models)->each(
            fn ($class) => $this->call('scout:import', ['model' => $class]),
        );

        return 0;
    }
}
