<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        Session::regenerate();

        // Smart Routing: Waar sturen we de bezoeker heen?
        if (Auth::user()->isAdmin()) {
            $this->redirect('/admin/dashboard', navigate: true);
        } else {
            $this->redirect('/', navigate: true);
        }
    }
}; ?>

<div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8 bg-white p-8 shadow-md rounded-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-indigo-600">
                Log in op je account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Welkom terug bij de Anime Webshop
            </p>
        </div>
        <form wire:submit="login" class="mt-8 space-y-6">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mailadres</label>
                <input wire:model="email" id="email" type="email" required autofocus
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Wachtwoord</label>
                <input wire:model="password" id="password" type="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center">
                <input wire:model="remember" id="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="remember" class="ml-2 block text-sm text-gray-900">Onthoud mij</label>
            </div>

            <div>
                <button type="submit" class="group relative flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Inloggen
                </button>
            </div>

            <div class="text-center mt-4 text-sm text-gray-600">
                Nog geen account?
                <a href="{{ route('register') }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500">
                    Registreer hier
                </a>
            </div>
        </form>
    </div>
</div>
