<?php

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component
{
    public string $name = '';
    public ?int $category_id = null;
    public string $description = '';
    public array $availableSizes = ['S', 'M', 'L', 'XL', 'XXL', 'One Size'];
    public array $variants = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string|max:50',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
            'category_id.required' => 'Selecteer aub een categorie.',
            'variants.required' => 'Selecteer minimaal één maat via de knoppen.',
            'variants.*.price.required' => 'Prijs is verplicht.',
            'variants.*.stock.required' => 'Voorraad mag niet leeg zijn.',
        ];
    }

    public function with(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
        ];
    }

    public function toggleSize(string $size): void
    {
        $existingIndex = collect($this->variants)->search(fn($v) => $v['size'] === $size);

        if ($existingIndex !== false) {
            unset($this->variants[$existingIndex]);
            $this->variants = array_values($this->variants);
        } else {
            // Nu standaard op 0 gezet voor een schone start
            $this->variants[] = ['size' => $size, 'price' => '', 'stock' => 0];
        }
    }

    public function isSizeSelected(string $size): bool
    {
        return collect($this->variants)->contains(fn($v) => $v['size'] === $size);
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $product = Product::create([
                'name' => $this->name,
                'category_id' => $this->category_id,
                'description' => $this->description,
            ]);

            foreach ($this->variants as $variantData) {
                $product->variants()->create([
                    'size' => $variantData['size'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);
            }
        });

        session()->flash('status', 'Product met geselecteerde maten succesvol toegevoegd!');
        $this->redirectRoute('admin.products.index', navigate: true);
    }
}; ?>

<div class="max-w-4xl mx-auto pb-10">
    <div class="flex items-center mb-6 space-x-4">
        <a href="{{ route('admin.products.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600">
            &larr; Terug
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Nieuwe Anime Merchandise</h1>
    </div>

    <form wire:submit="save" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">

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

        <h3 class="text-lg font-semibold border-b pb-2 mb-4">2. Selecteer Maten / Varianten</h3>

        <div class="flex flex-wrap gap-3 mb-6">
            @foreach($availableSizes as $size)
                <button type="button"
                        wire:click="toggleSize('{{ $size }}')"
                        class="px-5 py-2.5 rounded-md font-bold text-sm transition shadow-sm border {{ $this->isSizeSelected($size) ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                    {{ $size }}
                </button>
            @endforeach
        </div>
        @error('variants') <span class="text-red-500 text-xs block mb-4">{{ $message }}</span> @enderror

        @if(count($variants) > 0)
            <div class="space-y-4">
                @foreach($variants as $index => $variant)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end bg-gray-50 p-4 rounded-md border border-gray-200">

                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Geselecteerde Maat</span>
                            <div class="mt-1 font-bold text-indigo-600 text-lg py-1">
                                Maat {{ $variant['size'] }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Prijs (€)</label>
                            <input type="number" step="0.01" wire:model="variants.{{ $index }}.price" placeholder="0.00" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('variants.'.$index.'.price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Voorraad</label>
                            <input type="number" wire:model="variants.{{ $index }}.stock" class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('variants.'.$index.'.stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-sm text-gray-400 border border-dashed rounded-md bg-gray-50">
                Klik hierboven op de maten om varianten aan te maken.
            </div>
        @endif

        <div class="flex justify-end pt-6 border-t mt-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-md shadow-md transition">
                Opslaan in Magazijn
            </button>
        </div>
    </form>
</div>
