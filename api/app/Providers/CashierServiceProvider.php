<?php

namespace App\Providers;

use App\Models\Eloquent\Account;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class CashierServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Cashier::useCustomerModel(Account::class);
    }
}
