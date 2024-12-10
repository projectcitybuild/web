<?php

use Illuminate\Support\Facades\Route;

Route::permanentRedirect('terms', 'https://portal.projectcitybuild.com/books/legal-and-compliance/page/terms-of-service')
    ->name('terms');

Route::permanentRedirect('privacy', 'https://portal.projectcitybuild.com/books/legal-and-compliance/page/privacy-policy')
    ->name('privacy');

Route::permanentRedirect('wiki', 'https://wiki.projectcitybuild.com')
    ->name('wiki');

Route::permanentRedirect('report', 'https://docs.google.com/forms/d/e/1FAIpQLSerzdjDmc-xM26ZiDqIKN0d1gjjmRomFKg6efdHxXqir6QIMQ/viewform')
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

Route::permanentRedirect('monarch/apply', 'https://docs.google.com/forms/d/e/1FAIpQLSdm11-3-V8XgkGLAvXmacGgltc8C3VtQXIKHmHE2ZH5o9Ypmw/viewform?pli=1')
    ->name('monarch.apply');

Route::permanentRedirect('login', 'https://projectcitybuild.com/auth/login');

Route::permanentRedirect('register', 'https://projectcitybuild.com/auth/register');
