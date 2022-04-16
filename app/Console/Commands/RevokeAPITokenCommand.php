<?php

namespace App\Console\Commands;

use App\Entities\Models\Eloquent\Account;
use Illuminate\Console\Command;

class RevokeAPITokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-token:revoke';

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
        /** @var Account $account */
        $account = null;

        while ($account === null) {
            $accountId = $this->ask('Which account id does the token belong to?');

            $account = Account::find($accountId);
            if ($account === null) {
                $this->error('Account not found');
            }
        }

        $tokens = $account->tokens;

        if ($tokens->count() === 0) {
            $this->error('User has no issued API tokens');
            return 0;
        }

        $choices = $this->choice(
            question: 'Which tokens should be revoked?',
            choices: $tokens
                ->map(fn ($token, $key) => '#' . $key . ' ' . $token->name . ' (' . $token->created_at .')')
                ->toArray(),
            multiple: true,
        );

        $selectedTokens = collect($choices)
            ->map(function ($c) {
                $endPos = strpos(haystack: $c, needle: ' ') - 1;
                return substr(string: $c, offset: 1, length: $endPos);
            })
            ->map(fn ($index) => $tokens[$index]);

        foreach ($selectedTokens as $token) {
            $token->delete();
            $this->info('Revoked token: '.$token->name);
        }
    }
}
