<?php

use Illuminate\Support\Facades\Route;
use Library\Environment\Environment;

if (! Environment::isTest()) return;

Route::get('signed', fn () => 'test')
    ->name('test-signed')
    ->middleware('signed');

