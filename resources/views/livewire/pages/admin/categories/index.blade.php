<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public function with(): array
    {
        return [
            'categories' => Category::withCount('products')->latest()->paginate(10),
        ];
    }

    public function delete(Category $category): void
    {
        // Beveiliging: Check of er nog producten in zitten
        if ($category->products()->count() > 0) {
            session()->flash('error', 'Fout: Je kunt deze categorie niet verwijderen omdat er nog ' . $category->products()->count() . ' product(en) aan gekoppeld zijn.');
            return;
        }

        $category->delete();
        session()->flash('status', 'Categorie is succesvol verwijderd.');
    }
}; ?>

<div>
    @if (session('status'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            <span class="block sm:inline font-medium">{{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
            <span class="block sm:inline font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Categorieën Beheer</h1>

        <a href="{{ route('admin.categories.create') }}" wire:navigate class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition">
            + Nieuwe Categorie
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aantal Producten</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $category->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ $category->products_count }} items
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        <a href="{{ route('admin.categories.edit', $category) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 transition">Bewerk</a>

                        <button wire:click="delete({{ $category->id }})"
                                wire:confirm="Weet je zeker dat je de categorie '{{ $category->name }}' wilt verwijderen?"
                                class="text-red-600 hover:text-red-900 transition">
                            Verwijder
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                        Het magazijn heeft nog geen categorieën.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
