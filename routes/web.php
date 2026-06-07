<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Volt::route('/register', 'pages.auth.register')
    ->middleware('guest')
    ->name('register');
