<?php

use App\Models\BackgroundJobRun;

it('prunes job runs older than three months while retaining newer runs', function () {
    $this->travelTo('2026-11-23 02:00:00');

    $expiredRun = BackgroundJobRun::factory()->create([
        'created_at' => now()->subMonths(3)->subSecond(),
    ]);
    $retainedRun = BackgroundJobRun::factory()->create([
        'created_at' => now()->subMonths(3)->addSecond(),
    ]);

    $this->artisan('model:prune', ['--model' => [BackgroundJobRun::class]])
        ->assertExitCode(0);

    $this->assertModelMissing($expiredRun);
    $this->assertModelExists($retainedRun);
});
