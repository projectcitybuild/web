<?php

namespace App\Domains\Bans\UseCases;

use App\Models\GameIPBan;

final class GetActiveIPBan
{
    public function execute(string $ip): ?GameIPBan
    {
        return GameIPBan::where('ip_address', $ip)
            ->whereNull('unbanned_at')
            ->first();
    }
}
