<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public function with(): array
    {
        return [
            // Eager loading van category en variants voor maximale prestaties
            'products' => Product::with(['category', 'variants'])->latest()->paginate(10),
        ];
    }
}; ?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Magazijn: Anime Producten</h1>

        <a href="{{ route('admin.products.create') }}" wire:navigate class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition">
            + Nieuw Product
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varianten</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $product->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $product->category->name ?? 'Geen' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ $product->variants->count() }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Bewerk</a>
                        <button class="text-red-600 hover:text-red-900">Verwijder</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p>Het magazijn is nog leeg.</p>
                            <p class="text-xs text-gray-400 mt-1">Klik op '+ Nieuw Product' om je eerste Anime item toe te voegen.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
