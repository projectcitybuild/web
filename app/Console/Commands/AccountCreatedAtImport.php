<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Entities\Models\Eloquent\Account;
use Illuminate\Console\Command;

class AccountCreatedAtImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'one-off:createdatimport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import created at dates';

    private function getDataFileContents()
    {
        $data = file_get_contents(storage_path('app/id_created_at.json'));

        return json_decode($data, associative: true);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $entries = $this->getDataFileContents();
        $this->withProgressBar($entries, function ($entry) {
            $user = Account::find($entry['external_id']);

            if ($user == null) {
                $this->info("Skipping #{$entry['external_id']}");
            } else {
                $user->created_at = Carbon::parse($entry['created_at']);
                $user->disableLogging()->save();
            }
        });

        return 0;
    }
}
