<?php

use Illuminate\Support\Facades\Route;

Route::permanentRedirect('terms', 'https://portal.projectcitybuild.com/books/legal-and-compliance/page/terms-of-service')
    ->name('terms');

Route::permanentRedirect('privacy', 'https://portal.projectcitybuild.com/books/legal-and-compliance/page/privacy-policy')
    ->name('privacy');

Route::permanentRedirect('wiki', 'https://wiki.projectcitybuild.com')
    ->name('wiki');

Route::permanentRedirect('portal', 'https://portal.projectcitybuild.com/')
    ->name('portal');

Route::permanentRedirect('report', 'https://tally.so/r/lbbVQV')
    ->name('report');

Route::permanentRedirect('staff-apply', 'https://tally.so/r/yPMkZd')
    ->name('staff-apply');

Route::permanentRedirect('rules', 'https://portal.projectcitybuild.com/books/rules/page/community-rules')
    ->name('rules');

Route::permanentRedirect('ranks', 'https://portal.projectcitybuild.com/books/player-ranks/page/list-of-ranks')
    ->name('ranks');

Route::permanentRedirect('staff', 'https://portal.projectcitybuild.com/books/list-of-staff/page/active')
    ->name('staff');

Route::permanentRedirect('vote', 'https://portal.projectcitybuild.com/books/server-voting/page/server-voting')
    ->name('vote');

Route::permanentRedirect('map-archive', 'https://portal.projectcitybuild.com/books/map-archive/page/map-archive')
    ->name('map-archive');

Route::permanentRedirect('login', 'https://projectcitybuild.com/auth/login');

Route::permanentRedirect('register', 'https://projectcitybuild.com/auth/register');
