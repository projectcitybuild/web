<?php

use App\Core\Domains\Environment\Environment;
use Illuminate\Support\Facades\Route;

if (! Environment::isTest()) return;

Route::get('signed', fn () => 'test')
    ->name('test-signed')
    ->middleware('signed');

