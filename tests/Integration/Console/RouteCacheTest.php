<?php

namespace Tests\Integration\Console;

afterEach(function () {
    $this->artisan('route:clear');
});

it('caches the application routes', function () {
    $this->artisan('route:cache')
        ->assertExitCode(0);
});
