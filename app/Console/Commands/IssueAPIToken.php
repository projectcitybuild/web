<?php

namespace App\Console\Commands;

use App\Entities\Models\Eloquent\Account;
use Illuminate\Console\Command;
use Library\APITokens\APITokenScope;

class IssueAPIToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-token:issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an API token for a given account id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tokenName = $this->ask('Token name?');

        $accountId = $this->ask('Which account id should this token be issued to?');

        /** @var Account $account */
        $account = Account::find($accountId)
            ?? throw new \Exception('Account not found');

        if (! $this->confirm('Issue API token for '.$account->email.'?')) {
            return 0;
        }

        $scopes = $this->choice(
            question: 'What scopes should this token be granted?',
            choices: collect(APITokenScope::cases())
                ->map(fn ($s) => $s->value)
                ->toArray(),
            multiple: true,
        );

        $token = $account->createToken(
            name: $tokenName,
            abilities: $scopes,
        );

        $this->info('Token generated: '.$token->plainTextToken);
        $this->info('Scopes granted: '.implode(separator: ', ', array: $scopes);
    }
}
