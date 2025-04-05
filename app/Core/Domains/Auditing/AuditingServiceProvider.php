<?php

namespace App\Core\Domains\Auditing;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AuditingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::anonymousComponentNamespace('manage/components/audit/support', prefix: 'audit-support');
        Blade::componentNamespace('App\\Core\\Domains\\Auditing\\Components', prefix: 'audit');
    }
}
