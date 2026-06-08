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


// Beveiligde Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Volt::route('/dashboard', 'pages.admin.dashboard')->name('dashboard');
    //Producten routes
    Volt::route('/producten', 'pages.admin.products.index')->name('products.index');
    Volt::route('/producten/nieuw', 'pages.admin.products.create')->name('products.create');
    Volt::route('/producten/{product}/bewerken', 'pages.admin.products.edit')->name('products.edit');
    // Categorie Routes
    Volt::route('/categorieen', 'pages.admin.categories.index')->name('categories.index');
    Volt::route('/categorieen/nieuw', 'pages.admin.categories.create')->name('categories.create');
    Volt::route('/categorieen/{category}/bewerken', 'pages.admin.categories.edit')->name('categories.edit');
});
