<?php

use Illuminate\Support\Facades\Route;

Route::permanentRedirect('terms', 'https://forums.projectcitybuild.com/t/community-rules/22928')
    ->name('terms');

Route::permanentRedirect('privacy', 'https://forums.projectcitybuild.com/privacy')
    ->name('privacy');

Route::permanentRedirect('wiki', 'https://wiki.projectcitybuild.com')
    ->name('wiki');

Route::permanentRedirect('maps', 'https://maps.pcbmc.co')
    ->name('maps');

Route::permanentRedirect('3d-maps', 'https://3d.pcbmc.co')
    ->name('3d-maps');

Route::permanentRedirect('report', 'https://forums.projectcitybuild.com/w/player-report')
    ->name('report');

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
