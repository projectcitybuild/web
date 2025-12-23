<?php

use App\Domains\Homes\Data\HomeCount;

it('constructs', function () {
    $count = new HomeCount(
        used: 1,
        allowed: 2,
        sources: [
            'trusted' => 3,
        ],
    );
    expect($count->used)->toEqual(1);
    expect($count->allowed)->toEqual(2);
    expect($count->sources)->toEqual(['trusted' => 3]);
});

it('throws if sources is invalid format', function () {
    $invalid = [
        [1, 2],
        ['foo' => 'bar'],
        [1 => 2],
    ];
    foreach ($invalid as $sources) {
        expect(fn () => new HomeCount(
            used: 1,
            allowed: 2,
            sources: $sources,
        ))->toThrow(InvalidArgumentException::class);
    }
});
