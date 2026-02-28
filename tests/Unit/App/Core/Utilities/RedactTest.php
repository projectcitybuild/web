<?php

use App\Core\Utilities\Redact;

it('returns an empty string if email is empty', function () {
    expect(Redact::email(''))->toBe('');
});

it('redacts the name part of a standard email', function () {
    $email = 'john.doe@example.com';
    $redacted = Redact::email($email);

    // Name part should be partially redacted
    expect($redacted)->toBe('j*******@example.com');
});

it('always uses at least 3 asterisks for short names', function () {
    $email = 'ab@example.com';
    $redacted = Redact::email($email);

    // 'ab' has length 2, so max(2-1, 3) = 3 asterisks
    expect($redacted)->toBe('a***@example.com');
});

it('does not redact domain part', function () {
    $email = 'alice@mydomain.org';
    $redacted = Redact::email($email);

    expect($redacted)->toMatch('/^a\*+@mydomain\.org$/');
});

it('handles single character names correctly', function () {
    $email = 'x@domain.com';
    $redacted = Redact::email($email);

    // Single char name should be replaced with 3 asterisks
    expect($redacted)->toBe('x***@domain.com');
});

it('handles names longer than 3 characters', function () {
    $email = 'michael@domain.com';
    $redacted = Redact::email($email);

    // 7 chars in name, first character + 6 asterisks
    expect($redacted)->toBe('m******@domain.com');
});
