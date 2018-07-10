<?php
namespace App\Modules\Accounts\Repositories;

use App\Modules\Accounts\Models\AccountLink;
use App\Support\Repository;
use Carbon\Carbon;


class AccountLinkRepository extends Repository {

    protected $model = AccountLink::class;

    public function create(int $accountId,
                           string $providerName,
                           string $providerId,
                           string $providerEmail) : AccountLink 
    {
        return $this->getModel()->create([
            'provider_name'     => $providerName,
            'provider_id'       => $providerId,
            'provider_email'    => $providerEmail,
            'account_id'        => $accountId,
        ]);
    }

    public function update(int $accountId,
                           string $providerName,
                           string $providerId,
                           string $providerEmail) : AccountLink 
    {
        return $this->getModel()
            ->where('account_id', $accountId)
            ->save([
                'provider_name'     => $providerName,
                'provider_id'       => $providerId,
                'provider_email'    => $providerEmail,
            ]);
    }

    public function getByUserAndProvider(string $accountId, string $providerName) : ?AccountLink {
        return $this->getModel()
            ->where('provider_name', $providerName)
            ->where('account_id', $accountId)
            ->first();
    }

    public function getByProvider(string $name, string $id) : ?AccountLink {
        return $this->getModel()
            ->where('provider_name', $name)
            ->where('provider_id', $id)
            ->first();
    }

    public function getByEmail(string $email) : ?AccountLink {
        return $this->getModel()
            ->where('provider_email', $email)
            ->first();
    }

    public function getByProviderAccount(string $providerName, string $providerId) : ?AccountLink {
        return $this->getModel()
            ->where('provider_name', $providerName)
            ->where('provider_id', $providerId)
            ->first();
    }
    
}