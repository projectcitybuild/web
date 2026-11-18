<?php

namespace Tests\Unit\App\Rules;

use App\Core\Rules\DiscourseUsernameRule;

it('rejects a blank username', function () {
    expect((new DiscourseUsernameRule)->passes('username', ''))->toBeFalse();
});

it('rejects usernames shorter than the minimum length', function () {
    expect((new DiscourseUsernameRule)->passes('username', 'abc'))->toBeFalse();
});

it('accepts a username at the minimum length', function () {
    expect((new DiscourseUsernameRule)->passes('username', 'abcd'))->toBeTrue();
});

it('rejects usernames longer than the maximum length', function () {
    expect((new DiscourseUsernameRule)->passes('username', 'abcdefgjijklmnopqrstu'))->toBeFalse();
});

it('accepts a username at the maximum length', function () {
    expect((new DiscourseUsernameRule)->passes('username', 'abcdefgjijklmnopqrst'))->toBeTrue();
});

it('accepts valid username characters', function () {
    expect((new DiscourseUsernameRule)->passes('username', 'ab-cd.123_ABC-xYz'))->toBeTrue();
});

it('rejects invalid username characters', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', 'abc|'))->toBeFalse();
    expect($rule->passes('username', 'a#bc'))->toBeFalse();
    expect($rule->passes('username', 'abc xyz'))->toBeFalse();
});

it('allows usernames to start with alphanumeric characters or underscores', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', 'abcd'))->toBeTrue();
    expect($rule->passes('username', '1abc'))->toBeTrue();
    expect($rule->passes('username', '_abc'))->toBeTrue();
});

it('rejects usernames starting with dots or dashes', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', '.abc'))->toBeFalse();
    expect($rule->passes('username', '-abc'))->toBeFalse();
});

it('requires usernames to end with an alphanumeric character', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', 'abcd'))->toBeTrue();
    expect($rule->passes('username', 'abc9'))->toBeTrue();
});

it('rejects usernames ending with punctuation', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', 'abc_'))->toBeFalse();
    expect($rule->passes('username', 'abc.'))->toBeFalse();
    expect($rule->passes('username', 'abc-'))->toBeFalse();
});

it('rejects consecutive special characters', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', 'a__bc'))->toBeFalse();
    expect($rule->passes('username', 'a..bc'))->toBeFalse();
    expect($rule->passes('username', 'a--bc'))->toBeFalse();
});

it('rejects usernames ending in a file extension', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', 'abc.json'))->toBeFalse();
    expect($rule->passes('username', 'abc.png'))->toBeFalse();
});

it('rejects unicode characters', function () {
    $rule = new DiscourseUsernameRule;

    expect($rule->passes('username', 'abcö'))->toBeFalse();
    expect($rule->passes('username', 'abc象'))->toBeFalse();
});
