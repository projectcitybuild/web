<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\DatabaseTestCase;
use Tests\TestCase;

uses(
    TestCase::class,
    RefreshDatabase::class,
)->in('Integration', 'Unit');

uses(DatabaseTestCase::class)->in('Database');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
