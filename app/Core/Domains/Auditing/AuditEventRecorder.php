<?php

namespace App\Core\Domains\Auditing;

use App\Core\Domains\Auditing\Causers\SystemCauseResolver;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use OwenIt\Auditing\Resolvers\UserResolver;

class AuditEventRecorder
{
    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public function record(
        Model $subject,
        string $event,
        array $oldValues = [],
        array $newValues = [],
    ): ?Audit {
        if (
            ! Config::get('audit.enabled', true) ||
            (App::runningInConsole() && ! Config::get('audit.console', false))
        ) {
            return null;
        }

        $user = UserResolver::resolve();
        $command = Request::server('argv', []);
        $userModel = $user instanceof Model ? $user : null;

        return Audit::create([ // nosemgrep: php.lang.security.exec-use.exec-use -- argv is stored as audit metadata, never executed.
            'event' => $event,
            'auditable_type' => $subject->getMorphClass(),
            'auditable_id' => $subject->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_type' => $userModel?->getMorphClass(),
            'user_id' => $userModel?->getAuthIdentifier(),
            'url' => App::runningInConsole() && is_array($command)
                ? implode(' ', $command)
                : Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'tags' => SystemCauseResolver::getCauserName(),
        ]);
    }
}
