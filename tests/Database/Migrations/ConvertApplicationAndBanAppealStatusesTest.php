<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    DB::purge();
    DB::statement('CREATE TEMPORARY TABLE `ban_appeals` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `status` TINYINT UNSIGNED NOT NULL) ENGINE=InnoDB');
    DB::statement('CREATE TEMPORARY TABLE `builder_rank_applications` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `status` INT NOT NULL) ENGINE=InnoDB');
});

afterEach(function () {
    DB::disconnect();
});

it('round trips every supported status through both migration directions', function () {
    DB::table('ban_appeals')->insert(array_map(fn (int $status): array => ['status' => $status], [0, 1, 2, 3]));
    DB::table('builder_rank_applications')->insert(array_map(fn (int $status): array => ['status' => $status], [1, 2, 3]));

    $migration = require database_path('migrations/2026_11_18_231823_convert_application_and_ban_appeal_statuses_to_string_enums.php');
    $migration->up();

    expect(DB::table('ban_appeals')->orderBy('id')->pluck('status')->all())->toBe([
        'pending',
        'accepted_unban',
        'accepted_tempban',
        'denied',
    ]);
    expect(DB::table('builder_rank_applications')->orderBy('id')->pluck('status')->all())->toBe([
        'pending',
        'approved',
        'denied',
    ]);

    $migration->down();

    expect(DB::table('ban_appeals')->orderBy('id')->pluck('status')->map(fn ($status): int => (int) $status)->all())->toBe([0, 1, 2, 3]);
    expect(DB::table('builder_rank_applications')->orderBy('id')->pluck('status')->map(fn ($status): int => (int) $status)->all())->toBe([1, 2, 3]);

    $migration->up();

    expect(DB::table('ban_appeals')->orderBy('id')->pluck('status')->all())->toBe([
        'pending',
        'accepted_unban',
        'accepted_tempban',
        'denied',
    ]);
    expect(DB::table('builder_rank_applications')->orderBy('id')->pluck('status')->all())->toBe([
        'pending',
        'approved',
        'denied',
    ]);
});

it('rejects unknown statuses before converting either table', function (string $table, int|string $unknownStatus, string $direction, string $expectedColumnType) {
    DB::table('ban_appeals')->insert(['status' => 0]);
    DB::table('builder_rank_applications')->insert(['status' => 1]);

    $migration = require database_path('migrations/2026_11_18_231823_convert_application_and_ban_appeal_statuses_to_string_enums.php');

    if ($direction === 'down') {
        $migration->up();
        Schema::table($table, function ($blueprint) {
            $blueprint->string('status')->change();
        });
    }

    DB::table($table)->update(['status' => $unknownStatus]);

    expect(fn () => $migration->{$direction}())->toThrow(
        RuntimeException::class,
        sprintf('Cannot convert %s.status; unexpected values: %s', $table, json_encode([$unknownStatus], JSON_THROW_ON_ERROR)),
    );

    $column = DB::selectOne(sprintf("SHOW COLUMNS FROM `%s` WHERE Field = 'status'", $table));
    expect($column->Type)->toBe($expectedColumnType);
    expect(DB::table($table)->value('status'))->toBe($unknownStatus);
})->with([
    'unknown ban appeal legacy status' => ['ban_appeals', 99, 'up', 'tinyint(3) unsigned'],
    'unknown application legacy status' => ['builder_rank_applications', 99, 'up', 'int(11)'],
    'unknown ban appeal string status' => ['ban_appeals', 'invalid', 'down', 'varchar(191)'],
    'unknown application string status' => ['builder_rank_applications', 'invalid', 'down', 'varchar(191)'],
]);
