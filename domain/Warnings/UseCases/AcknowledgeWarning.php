<?php

namespace Domain\Warnings\UseCases;

use Entities\Models\Eloquent\PlayerWarning;
use Repositories\PlayerWarningRepository;

final class AcknowledgeWarning
{
    public function __construct(
        private readonly PlayerWarningRepository $playerWarningRepository,
    ) {
    }

    public function execute(int $warningId): PlayerWarning
    {
        $warning = $this->playerWarningRepository->find($warningId);
        if ($warning === null) {
            abort(404);
        }

        $this->playerWarningRepository->acknowledge($warning);

        return $warning;
    }
}
