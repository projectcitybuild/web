<?php
namespace Entities\Accounts\Repositories;

use Entities\Accounts\Models\AccountPasswordReset;
use Application\Contracts\Repository;
use Carbon\Carbon;

class AccountPasswordResetRepository extends Repository
{
    protected $model = AccountPasswordReset::class;

    public function updateOrCreateByEmail(
        string $email,
        string $token
    ) : AccountPasswordReset {
        return $this->getModel()->updateOrCreate([
            'email' => $email,
        ], [
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);
    }

    public function getByEmail(string $email) : ?AccountPasswordReset
    {
        return $this->getModel()
            ->where('email', $email)
            ->first();
    }

    public function getByToken(string $token) : ?AccountPasswordReset
    {
        return $this->getModel()
            ->where('token', $token)
            ->first();
    }

    public function deleteOlderThan(Carbon $date)
    {
        return $this->getModel()
            ->whereDate('created_at', '<', $date)
            ->delete();
    }
}
