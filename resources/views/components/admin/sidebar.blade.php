<aside class="w-64 bg-indigo-900 text-white flex flex-col h-screen">
    <div class="p-6 text-xl font-bold border-b border-indigo-800 tracking-wider">ANIME ADMIN</div>
    <nav class="flex-1 p-4 space-y-2">

        <a href="{{ route('admin.dashboard') }}"
           wire:navigate
           class="block p-2 rounded transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-800 font-semibold shadow-inner' : 'hover:bg-indigo-800' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.products.index') }}"
           wire:navigate
           class="block p-2 rounded transition {{ request()->routeIs('admin.products.*') ? 'bg-indigo-800 font-semibold shadow-inner' : 'hover:bg-indigo-800' }}">
            Producten
        </a>

        <a href="#"
           class="block p-2 rounded transition hover:bg-indigo-800">
            Gebruikers
        </a>

    </nav>
</aside>
