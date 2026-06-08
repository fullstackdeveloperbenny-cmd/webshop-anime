<?php

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component
{
    public Product $product;

    // Basis Product Velden
    public string $name = '';
    public ?int $category_id = null;
    public string $description = '';

    // Dezelfde maten-knoppen als op de create-pagina voor 100% UX-consistentie
    public array $availableSizes = ['S', 'M', 'L', 'XL', 'XXL', 'One Size'];

    // Array voor de varianten op het scherm
    public array $variants = [];

    // Lijst met ID's die uit de database verwijderd moeten worden
    public array $variantsToDelete = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer', // Nullable, want gloednieuwe maten hebben nog geen ID!
            'variants.*.size' => 'required|string|max:50',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'variants.min' => 'Een product moet minimaal één maat/variant hebben.',
            'variants.*.price.required' => 'Prijs is verplicht.',
            'variants.*.stock.required' => 'Voorraad is verplicht.',
        ];
    }

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->description = $product->description ?? '';

        // Haal de bestaande varianten op uit de database
        $this->variants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'size' => $variant->size,
                'price' => $variant->price,
                'stock' => $variant->stock,
            ];
        })->toArray();
    }

    public function with(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
        ];
    }

    // Actie die reageert op de knoppen (Maten aan- of uitzetten)
    public function toggleSize(string $size): void
    {
        $existingIndex = collect($this->variants)->search(fn($v) => $v['size'] === $size);

        if ($existingIndex !== false) {
            // Als de maat al bestond én een database-ID heeft, onthoud hem dan voor de delete query
            if (!empty($this->variants[$existingIndex]['id'])) {
                $this->variantsToDelete[] = $this->variants[$existingIndex]['id'];
            }
            unset($this->variants[$existingIndex]);
            $this->variants = array_values($this->variants);
        } else {
            // Voeg een GLOEDNIEUWE maat toe (id is null)
            $this->variants[] = ['id' => null, 'size' => $size, 'price' => '', 'stock' => 0];
        }
    }

    // Check of de knop paars (actief) moet zijn
    public function isSizeSelected(string $size): bool
    {
        return collect($this->variants)->contains(fn($v) => $v['size'] === $size);
    }

    public function update(): void
    {
        $this->validate();

        DB::transaction(function () {
            // 1. Update de basisgegevens
            $this->product->update([
                'name' => $this->name,
                'category_id' => $this->category_id,
                'description' => $this->description,
            ]);

            // 2. Verwijder de maten die zijn uitgeklikt
            if (!empty($this->variantsToDelete)) {
                $this->product->variants()->whereIn('id', $this->variantsToDelete)->delete();
            }

            // 3. Loop door de varianten op je scherm
            foreach ($this->variants as $variantData) {
                if (!empty($variantData['id'])) {
                    // Bestaande variant? Netjes updaten
                    $this->product->variants()->where('id', $variantData['id'])->update([
                        'size' => $variantData['size'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);
                } else {
                    // Geen ID? Dan is hij splinternieuw toegevoegd tijdens het editen!
                    $this->product->variants()->create([
                        'size' => $variantData['size'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);
                }
            }
        });

        session()->flash('status', 'Product en varianten succesvol bijgewerkt!');
        $this->redirectRoute('admin.products.index', navigate: true);
    }
}; ?>

<div class="max-w-4xl mx-auto pb-10">
    <div class="flex items-center mb-6 space-x-4">
        <a href="{{ route('admin.products.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600 transition">
            &larr; Terug
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Product Bewerken: {{ $product->name }}</h1>
    </div>

    <form wire:submit="update" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">

        <h3 class="text-lg font-semibold border-b pb-2 mb-4">1. Basisgegevens</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Productnaam</label>
                <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Categorie</label>
                <select wire:model="category_id" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Selecteer een categorie...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700">Beschrijving</label>
            <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <h3 class="text-lg font-semibold border-b pb-2 mb-4">2. Maten / Varianten Beheren</h3>

        <div class="flex flex-wrap gap-3 mb-6">
            @foreach($availableSizes as $size)
                <button type="button"
                        wire:click="toggleSize('{{ $size }}')"
                        class="px-5 py-2.5 rounded-md font-bold text-sm transition shadow-sm border {{ $this->isSizeSelected($size) ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                    {{ $size }}
                </button>
            @endforeach
        </div>
        @error('variants') <span class="text-red-500 text-xs block mb-4 font-medium">{{ $message }}</span> @enderror

        <div class="space-y-4">
            @foreach($variants as $index => $variant)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end bg-gray-50 p-4 rounded-md border border-gray-200">

                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Maat</span>
                        <div class="mt-1 font-bold text-indigo-600 text-lg py-1 flex items-center justify-between">
                            <span>Maat {{ $variant['size'] }}</span>
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded {{ !empty($variant['id']) ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ !empty($variant['id']) ? 'Bestaand' : 'Nieuw' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Prijs (€)</label>
                        <input type="number" step="0.01" wire:model="variants.{{ $index }}.price" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white">
                        @error('variants.'.$index.'.price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Voorraad</label>
                        <input type="number" wire:model="variants.{{ $index }}.stock" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white">
                        @error('variants.'.$index.'.stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>
            @endforeach
        </div>

        <div class="flex justify-end pt-6 border-t mt-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-md shadow-md transition">
                Wijzigingen Opslaan
            </button>
        </div>
    </form>
</div>
