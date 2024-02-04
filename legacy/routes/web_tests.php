<?php

use Illuminate\Support\Facades\Route;

Route::get('signed', fn () => 'test')
    ->name('test-signed')
    ->middleware('signed');
