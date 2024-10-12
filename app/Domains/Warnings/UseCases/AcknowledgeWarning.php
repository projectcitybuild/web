<?php

namespace App\Domains\Warnings\UseCases;

use App\Models\PlayerWarning;

final class AcknowledgeWarning
{
    public function execute(int $warningId, ?int $accountId = null): PlayerWarning
    {
        $warning = PlayerWarning::find($warningId);
        if ($warning === null) {
            abort(404);
        }
        if ($accountId !== null && $warning->warnedPlayer->account?->getKey() !== $accountId) {
            abort(403);
        }
        if ($warning->is_acknowledged && $warning->acknowledged_at !== null) {
            abort(410);
        }

        $warning->is_acknowledged = true;
        $warning->acknowledged_at = now();
        $warning->save();

        return $warning;
    }
}
