<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use Symfony\Component\DomCrawler\Crawler;

it('shows an empty state when the account has no donations', function () {
    $this->actingAs(Account::factory()->create());

    $this->get(route('front.account.donations'))
        ->assertOk()
        ->assertSee('You have not made any donations');
});

it('marks the donations link as current on the donations page', function () {
    $this->actingAs(Account::factory()->create());

    $response = $this->get(route('front.account.donations'))
        ->assertOk();

    $currentLinks = (new Crawler($response->getContent()))
        ->filterXPath('//nav[@data-sticky-navbar]//a[@aria-current="page"]');

    expect($currentLinks->count())->toBe(1)
        ->and($currentLinks->text())->toBe('Donations');
});
