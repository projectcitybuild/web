<?php

namespace App\Core\Domains\Auditing;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AuditingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::anonymousComponentNamespace('library/audit/support', 'audit-support');
        Blade::componentNamespace('Library\\Auditing\\Components', 'audit');
    }
}
