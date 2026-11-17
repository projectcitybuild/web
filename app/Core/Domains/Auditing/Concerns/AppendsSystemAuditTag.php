<?php

namespace App\Core\Domains\Auditing\Concerns;

use App\Core\Domains\Auditing\Causers\SystemCauseResolver;

trait AppendsSystemAuditTag
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function appendSystemAuditTag(array $data): array
    {
        $systemCauser = SystemCauseResolver::getCauserName(); // nosemgrep: php.lang.security.exec-use.exec-use -- reads an enum-backed audit tag, no command is run.

        if ($systemCauser !== null) {
            $tags = array_filter(explode(',', $data['tags'] ?? ''));
            $tags[] = $systemCauser;
            $data['tags'] = implode(',', array_unique($tags));
        }

        return $data;
    }
}
