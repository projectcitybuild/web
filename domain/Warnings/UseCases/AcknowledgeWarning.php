<?php

namespace Domain\Warnings\UseCases;

use App\Models\PlayerWarning;
use Repositories\PlayerWarnings\PlayerWarningRepository;

final class AcknowledgeWarning
{
    public function __construct(
        private readonly PlayerWarningRepository $playerWarningRepository,
    ) {
    }

    public function execute(int $warningId, ?int $accountId = null): PlayerWarning
    {
        $warning = $this->playerWarningRepository->find($warningId);
        if ($warning === null) {
            abort(404);
        }
        if ($accountId !== null && $warning->warnedPlayer->account?->getKey() !== $accountId) {
            abort(403);
        }
        if ($warning->is_acknowledged && $warning->acknowledged_at !== null) {
            abort(410);
        }

        return $this->playerWarningRepository->acknowledge($warning);
    }
}
