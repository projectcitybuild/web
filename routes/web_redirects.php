<?php

use Illuminate\Support\Facades\Route;

Route::permanentRedirect('terms', 'https://forums.projectcitybuild.com/t/community-rules/22928')->name('terms');
Route::permanentRedirect('privacy', 'https://forums.projectcitybuild.com/privacy')->name('privacy');
Route::permanentRedirect('wiki', 'https://wiki.projectcitybuild.com')->name('wiki');
Route::permanentRedirect('maps', 'https://maps.pcbmc.co')->name('maps');
Route::permanentRedirect('3d-maps', 'https://3d.pcbmc.co')->name('3d-maps');
Route::permanentRedirect('report', 'https://forums.projectcitybuild.com/w/player-report')->name('report');
