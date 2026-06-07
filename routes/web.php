<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Volt::route('/login', 'pages.auth.login')
    ->middleware('guest')
    ->name('login');

Volt::route('/register', 'pages.auth.register')
    ->middleware('guest')
    ->name('register');
