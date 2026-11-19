<?php

it('returns configured version metadata without authentication', function () {
    config([
        'app.name' => 'Project City Build',
        'app.version' => '1.8.0',
        'app.commit' => '0123456789abcdef0123456789abcdef01234567',
        'sentry.environment' => 'production',
    ]);

    $this->getJson(route('version'))
        ->assertOk()
        ->assertExactJson([
            'name' => 'Project City Build',
            'version' => '1.8.0',
            'commit' => '0123456789abcdef0123456789abcdef01234567',
            'environment' => 'testing',
        ]);
});
