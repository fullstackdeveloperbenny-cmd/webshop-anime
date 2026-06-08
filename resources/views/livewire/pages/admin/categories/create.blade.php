<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component
{
    #[Validate('required|string|max:255|unique:categories,name')]
    public string $name = '';

    public function save(): void
    {
        $this->validate();

        Category::create(['name' => $this->name]);

        session()->flash('status', 'Categorie succesvol aangemaakt!');
        $this->redirectRoute('admin.categories.index', navigate: true);
    }
}; ?>

<div class="max-w-3xl mx-auto">
    <div class="flex items-center mb-6 space-x-4">
        <a href="{{ route('admin.categories.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600 transition">
            &larr; Terug
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Nieuwe Categorie</h1>
    </div>

    <form wire:submit="save" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Categorienaam</label>
            <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="bijv. Action Figures">
            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-sm transition">
                Aanmaken
            </button>
        </div>
    </form>
</div>
