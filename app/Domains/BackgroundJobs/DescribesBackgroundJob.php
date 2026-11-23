<?php

namespace App\Domains\BackgroundJobs;

interface DescribesBackgroundJob
{
    /**
     * @return array<string, scalar|null>
     */
    public function backgroundJobMetadata(): array;
}
