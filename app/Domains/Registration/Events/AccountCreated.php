<?php

namespace App\Domains\Registration\Events;

use App\Models\Account;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Account $account,
    ) {}
}
