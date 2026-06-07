<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public function mount(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Toegang geweigerd. Deze sectie is uitsluitend voor beheerders.');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/login', navigate: true);
    }
}; ?>

<div class="min-h-screen bg-gray-100">
    <nav class="bg-indigo-900 border-b border-indigo-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-white tracking-widest">ANIME WEBSHOP - BACKOFFICE</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-indigo-200 text-sm">
                        Ingelogd als: <strong class="text-white">{{ Auth::user()->name }}</strong>
                    </span>
                    <button wire:click="logout" class="text-sm bg-indigo-700 hover:bg-indigo-600 text-white px-3 py-2 rounded-md font-medium transition">
                        Uitloggen
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-semibold mb-4">Welkom op het Admin Dashboard</h1>
                    <p class="text-gray-600">
                        Vanaf hier gaan we in de volgende stappen de CRUD acties voor de producten bouwen, inclusief Soft Deletes!
                    </p>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center shadow-sm">
                            <h3 class="font-bold text-lg text-gray-800 mb-2">Producten</h3>
                            <p class="text-sm text-gray-500 mb-4">Beheer je anime catalogus</p>
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">Komt binnenkort</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
