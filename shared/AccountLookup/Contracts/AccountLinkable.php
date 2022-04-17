<?php

namespace Shared\AccountLookup\Contracts;

use App\Entities\Models\Eloquent\Account;

interface AccountLinkable
{
    public function getLinkedAccount(): ?Account;
}
