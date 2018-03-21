<?php
namespace App\Modules\Accounts\Repositories;

use App\Modules\Accounts\Models\Account;
use App\Shared\Repository;
use Carbon\Carbon;


class AccountRepository extends Repository {

    protected $model = Account::class;

    public function create(
        string $email, 
        string $password,
        string $ip
    ) : Account {

        return $this->getModel()->create([
            'email'         => $email,
            'password'      => $password,
            'remember_token' => '',
            'last_login_ip' => $ip,
            'last_login_at' => Carbon::now(),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ]);
    }
    
}