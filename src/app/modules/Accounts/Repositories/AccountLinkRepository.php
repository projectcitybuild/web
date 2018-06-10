<?php
namespace App\Modules\Accounts\Repositories;

use App\Modules\Accounts\Models\AccountLink;
use App\core\Repository;
use Carbon\Carbon;


class AccountLinkRepository extends Repository {

    protected $model = AccountLink::class;

    public function create(
        string $providerName,
        string $providerId,
        int $accountId
    ) : AccountLink {

        return $this->getModel()->create([
            'provider_name' => $providerName,
            'provider_id'   => $providerId,
            'account_id'    => $accountId,
        ]);
    }

    public function getByProvider(string $name, string $id) : ?AccountLink {
        return $this->getModel()
            ->where('provider_name', $name)
            ->where('provider_id', $id)
            ->first();
    }
    
}