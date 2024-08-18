<?php

namespace App\Core\Support\Cashier;

use App\Models\Account;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

final class CashierServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Cashier::useCustomerModel(Account::class);
    }
}
