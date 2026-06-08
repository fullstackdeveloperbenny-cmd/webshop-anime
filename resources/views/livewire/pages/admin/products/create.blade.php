<?php

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component
{
    // Product Velden
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|exists:categories,id')]
    public ?int $category_id = null;

    #[Validate('nullable|string')]
    public string $description = '';

    // Eerste Variant Velden
    #[Validate('required|string|max:50')]
    public string $size = '';

    #[Validate('required|numeric|min:0')]
    public string $price = '';

    #[Validate('required|integer|min:0')]
    public int $stock = 0;

    public function with(): array
    {
        return [
            // We laden de categorieën in voor de dropdown
            'categories' => Category::orderBy('name')->get(),
        ];
    }

    public function save()
    {
        $this->validate();

        // Database Transaction: Alles of niets
        DB::transaction(function () {
            $product = Product::create([
                'name' => $this->name,
                'category_id' => $this->category_id,
                'description' => $this->description,
            ]);

            $product->variants()->create([
                'size' => $this->size,
                'price' => $this->price,
                'stock' => $this->stock,
            ]);
        });

        // Succesbericht en terug naar index
        session()->flash('status', 'Product succesvol toegevoegd aan het magazijn!');
        $this->redirectRoute('admin.products.index', navigate: true);
    }
}; ?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6 space-x-4">
        <a href="{{ route('admin.products.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600">
            &larr; Terug
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Nieuw Product Toevoegen</h1>
    </div>

    <form wire:submit="save" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">

        <h3 class="text-lg font-semibold border-b pb-2 mb-4">1. Basisgegevens</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Productnaam</label>
                <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Categorie</label>
                <select wire:model="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Selecteer een categorie...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700">Beschrijving</label>
            <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <h3 class="text-lg font-semibold border-b pb-2 mb-4">2. Eerste Variant (Maat/Prijs)</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Maat (bijv. M, L, One-Size)</label>
                <input type="text" wire:model="size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('size') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Prijs (€)</label>
                <input type="number" step="0.01" wire:model="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Voorraad</label>
                <input type="number" wire:model="stock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t mt-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-sm transition">
                Opslaan in Magazijn
            </button>
        </div>
    </form>
</div>
