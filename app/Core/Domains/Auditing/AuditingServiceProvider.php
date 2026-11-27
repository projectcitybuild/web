<?php

namespace App\Core\Domains\Auditing;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AuditingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::anonymousComponentPath(resource_path('views/admin/manage/components/audit/support'), prefix: 'audit-support');
        Blade::anonymousComponentPath(resource_path('views/admin/manage/components/audit/diffs'), prefix: 'audit');
        Blade::componentNamespace('App\\Core\\Domains\\Auditing\\Components', prefix: 'audit');
    }
}
